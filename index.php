<?php
include_once('lib/config.php');
include_once('lib/Renderer.php');
include_once('classes/User.php');
include_once('classes/Files.php');

$user = new User();

$renderer = new Renderer('layouts/overall.tpl');

$path = isset($_GET['path']) ? $_GET['path'] : false;
$mod = isset($_GET['mod']) ? $_GET['mod'] : false;
$token = isset($_POST['token']) ? $_POST['token'] : false;
$hasUser = $user->loadCurrent();

if ($path)
{
	if ($hasUser)
	{
		$renderer->setNav('nav/dashboard.tpl');
		switch ($path)
		{
			case 'account':
				$files = new Files();
				$files->getBy(array('_user_id' => $user->getAttribute('user_id')));
				$id = isset($_POST['id']) ? $_POST['id'] : false;
				$password1 = isset($_POST['password1']) ? $_POST['password1'] : false;
				$password2 = isset($_POST['password2']) ? $_POST['password2'] : false;
				$passwordError = false;
				$passwordMessage = false;
				$deleteError = false;
				
				if ($mod && $mod === 'update')
				{
					if ($password1 === $password2 && $token === $_SESSION['token'])
					{
						$user->setPassword($password1);
						
						if ($user->save())
						{
							$passwordMessage = "Your password has been changed";
						}
						else
						{
							$passwordError = 'There was a problem saving your password';
						}
					}
					else
					{
						$passwordError = "Your passwords didn't match! Give it another shot.";
					}
				}
				else if ($mod && $mod === 'delete')
				{
					if ($id && $token === $_SESSION['token'] && $user->getAttribute('user_id') === $id)
					{
						if ($user->delete())
						{
							header('Location: '.WEB_ROOT.'/logout');
							exit();
						}
					}
					
					$deleteError = 'There was a problem deleting your account.';
				}
				
				$renderer->setTitle('Your info here');
				$renderer->setKeywords(array(
					'your',
					'info',
					'here'
				));
				$renderer->setDescription('Manage your account');
				$renderer->addContent('account.tpl', array(
					'user' => $user,
					'filesCount' => count($files->getElements()),
					'passwordError' => $passwordError,
					'deleteError' => $deleteError,
					'token' => $_SESSION['token'],
					'passwordMessage' => $passwordMessage
				));
				break;
			case 'files':
				$files = new Files();
				$files->orderby('created DESC');
				$files->getBy(array('_user_id' => $user->getAttribute('user_id')));
				
				if ($mod && $mod === 'json')
				{
					header('Content-Type: application/json');
					echo $files->getJSON();
					exit();
				}
				else if ($mod && $mod === 'delete')
				{
					header('Content-Type: application/json');
					
					if ($token === $_SESSION['token'])
					{
						$id = isset($_POST['id']) ? $_POST['id'] : false;
						$file = new File(array(
							'file_id' => $id,
							'_user_id' => $user->getAttribute('user_id')
						));
						
						if ($file->loadByAttributes())
						{
							if ($file->delete())
							{
								echo json_encode(array('deleted' => $file->getAttributes()));
								exit();
							}
						}
					}
					
					echo json_encode(array('error' => 'Could not delete that file'));
					exit();
				}
				else if ($mod && $mod === 'upload')
				{
					if ($token === $_SESSION['token'])
					{
						$target = isset($_FILES['file']) ? $_FILES['file'] : false;
						
						if ($target)
						{
							$location = FILES_DIR.'/'.$user->getAttribute('user_id').'/'.$target['name'];
							
							if (move_uploaded_file($target['tmp_name'], $location))
							{
								$file = new File();
								$file->setAttribute('name', $target['name']);
								$file->setAttribute('location', $location);
								
								$test = $file->loadByAttributes();
								$file->setAttribute('mime_type', $target['type']);
								$file->setAttribute('size', $target['size']);
								$file->setAttribute('_user_id', $user->getAttribute('user_id'));
								$file->setAttribute('created', date('Y:m:d H:i:s'));
								
								if ($file->save())
								{
									$file->loadByAttributes();
									
									if ($test)
									{
										
										echo json_encode(array('updated' => $file->getAttributes()));
									}
									else
									{
										echo $file->getJSON();
									}
									exit();
								}
								else
								{
									echo json_encode(array('error' => 'Your file was uploaded, but not saved to the database.'));
									exit();
								}
							}
						}
					}
					
					echo json_encode(array('error' => 'There was a problem uploading your file'));
					exit();
				}
				else if ($mod && $mod === 'download')
				{
					$id = isset($_GET['id']) ? $_GET['id'] : false;
					
					if ($id)
					{
						$file = new File(array('file_id' => $id, '_user_id' => $user->getAttribute('user_id')));
						
						if ($file->loadByAttributes())
						{
							header('Cache-Control: public');
							header('Content-Description: File Transfer');
							header('Content-Disposition: attachment; filename='.$file->getAttribute('name'));
							header('Content-Type: '.$file->getAttribute('mime_type'));
							header('Content-Transfer-Encodeing: binary');
							
							readfile($file->getAttribute('location'));
						}
						else
						{
							header('Content-Type: text/plain');
							print_r($file->getAttributes());
							print_r($file->getError());
						}
					}
					
					header('HTTP/1.0 404 Not Found');
					exit();
				}
				else
				{
					$renderer->setTitle('Your stuff');
					$renderer->setKeywords(array(
						'your',
						'stuff',
						'here'
					));
					$renderer->addScript('scripts/upload.js');
					$renderer->addScript('scripts/json_parse.js');
					$renderer->addContent('dashboard.tpl', array('files' => $files->getElements(), 'token' => $_SESSION['token'], 'filesCount' => count($files->getElements())));
				}
				break;
			case 'logout':
				session_unset();
				session_destroy();
				header('Location: '.WEB_ROOT);
				exit();
				break;
			case 'about':
				$renderer->setTitle('About');
				$renderer->setKeywords(array(
					'store',
					'files',
					'upload',
					'easy',
					'fast',
					'anywhere',
					'ownership',
					'licensing',
					'responsibility'
				));
				$renderer->setDescription('What is stor.me? Why should I use it? What happens when I do use it?');
				$renderer->addContent('about.tpl');
				break;
			default:
				header('Location: '.WEB_ROOT.'/files/');
				exit();
		}
	}
	else
	{
		$renderer->setNav('nav/index.tpl');
		switch ($path)
		{
			case 'signup':
				$renderer->setTitle('Create an account');
				$renderer->setKeywords(array(
					'store',
					'files',
					'upload',
					'easy',
					'fast',
					'anywhere'
				));
				$renderer->setDescription('Sign up for easy file storage, right from your browser!');
				$user = new User();
				$error = false;
				
				if (count($_POST) > 0)
				{
					if ($user->loadByAttributes(array('email' => $_POST['email'])))
					{
						$error = "There's already an account with that email";
					}
					else if ($_POST['password1'] !== $_POST['password2'])
					{
						$error = "Your passwords don't match! Give it another shot.";
					}
					else
					{
						$user->setAttributes($_POST);
						$user->setPassword($_POST['password1']);
						
						if ($user->save())
						{
							if ($user->login())
							{
								mkdir(FILES_DIR.'/'.$user->getAttribute('user_id'));
								header('Location: '.WEB_ROOT.'/files');
								exit();
							}
							else
							{
								$error = 'Could not log in';
							}
						}
						else
						{
							$error = 'Error saving';
						}
					}
				}
				
				$renderer->addContent('signup.tpl', array('user' => $user, 'error' => $error));
				break;
			case 'login':
				$renderer->setTitle('Login');
				$renderer->setKeywords(array(
					'store',
					'files',
					'upload',
					'easy',
					'fast',
					'anywhere'
				));
				$renderer->setDescription('Sign up for easy file storage, right from your browser!');
			
				if (count($_POST) > 0)
				{
					$user->setAttribute('email', $_POST['email']);
					$user->setPassword($_POST['password']);
					
					if ($user->login())
					{
						header('Location: '.WEB_ROOT.'/files');
						exit();
					}
					else
					{
						print_r($user->getError());
						print_r($user->getAttributes());
						$renderer->addContent('login.tpl', array('user' => $user, 'error' => 'Uh-oh. Was that the right password? Do you have an account?'));
					}
				}
				else
				{
					$renderer->addContent('login.tpl', array('user' => $user));
				}
				
				break;
			case 'about':
				$renderer->setTitle('About');
				$renderer->setKeywords(array(
					'store',
					'files',
					'upload',
					'easy',
					'fast',
					'anywhere',
					'ownership',
					'licensing',
					'responsibility'
				));
				$renderer->setDescription('What is stor.me? Why should I use it? What happens when I do use it?');
				$renderer->addContent('about.tpl');
				break;
			default:
				header('Location: '.WEB_ROOT.'/login');
				exit();
		}
	}
}
else
{
	if ($hasUser)
	{
		header('Location: '.WEB_ROOT.'/files');
		exit();
	}
	else
	{
		$renderer->setNav('nav/index.tpl');
		$renderer->setTitle('Put your stuff here');
		$renderer->setKeywords(array(
			'store',
			'files',
			'upload',
			'easy',
			'fast',
			'anywhere'
		));
		$renderer->setDescription('Easy, cloud-based storage for your stuff. Any file, any size, anywhere.');
		$renderer->addContent('index.tpl');
	}
}

$renderer->render();
?>