<?php
include_once('classes/Users.php');
include_once('lib/Controller.php');

class AccountController extends Controller
{
	protected $layout = 'layouts/overall.tpl';
	protected $nav = 'nav/dashboard.tpl';

	public function index($user)
	{
		$this->setTitle('Your info here');
		$this->setKeywords(array(
			'your',
			'info',
			'here'
		));
		$this->setDescription('Manage your accont');
		$this->addContent('account.tpl', array(
			'user' => $user,
			'filesCount' => count($user->getFiles()),
			'token' => $_SESSION['token']
		));
		$this->view();
	}

	public function login()
	{
		$error = false;
		$user = new User();

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
				$error = 'Uh-oh. Was that the right password? Do you have an account?';
			}
		}

		$this->setNav('nav/index.tpl');
		$this->setTitle('Login');
		$this->setKeywords(array(
			'store',
			'files',
			'upload',
			'easy',
			'fast',
			'anywhere'
		));
		$this->setDescription('Sign up for easy file storage, right from your browser!');
		$this->addcontent('login.tpl', array(
			'user' => $user,
			'error' => $error
		));

		$this->view();
	}

	public function logout($user)
	{
		session_unset();
		session_destroy();
		header('Location: '.WEB_ROOT);
		exit();
	}

	public function create()
	{
		$error = false;
		$user = new User();

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
						$error = "Couldn't log in.";
					}
				}
				else
				{
					$error = 'There was a problem creating your account.';
				}
			}
		}

		$this->setNav('nav/index.tpl');
		$this->setTitle('Create an account');
		$this->setKeywords(array(
			'store',
			'files',
			'upload',
			'easy',
			'fast',
			'anywhere'
		));
		$this->setDescription('Sign up for easy file storage, right from your browser!');
		$this->addContent('signup.tpl', array(
			'user' => $user,
			'error' => $error
		));
		$this->view();
	}

	public function update($user)
	{
		$passwordError = false;
		$passwordMessage = false;

		$token = isset($_POST['token']) ? $_POST['token'] : false;
		$password1 = isset($_POST['password1']) ? $_POST['password1'] : false;
		$password2 = isset($_POST['password2']) ? $_POST['password2'] : false;

		if ($token === $_SESSION['token'])
		{
			if ($password1 === $password2)
			{
				$user->setPassword($password1);

				if ($user->save())
				{
					$passwordMessage = 'Your password has been changed';
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
		else
		{
			$passwordError = 'Invalid request';
		}

		$this->setTitle('Your info here');
		$this->setKeywords(array(
			'your',
			'info',
			'here'
		));
		$this->setDescription('Manage your accont');
		$this->addContent('account.tpl', array(
			'user' => $user,
			'filesCount' => count($user->getFiles()),
			'passwordError' => $passwordError,
			'token' => $_SESSION['token'],
			'passwordMessage' => $passwordMessage
		));
		$this->view();
	}

	public function delete($user)
	{
		$deleteError = false;

		$id = isset($_POST['id']) ? $_POST['id'] : false;
		$token = isset($_POST['token']) ? $_POST['token'] : false;

		if ($token === $_SESSION['token'])
		{
			if ($id && $user->getAttribute('user_id') === $id)
			{
				if ($user->delete())
				{
					header('Location: '.WEB_ROOT.'/logout');
					exit();
				}
			}
			else
			{
				$deleteError = 'Wrong user!';
			}
		}
		else
		{
			$deleteError = 'Invalid request';
		}

		$this->setTitle('Your info here');
		$this->setKeywords(array(
			'your',
			'info',
			'here'
		));
		$this->setDescription('Manage your account');
		$this->addContent('account.tpl', array(
			'user' => $user,
			'filesCount' => count($user->getFiles()),
			'deleteError' => $deleteError,
			'token' => $_SESSION['token']
		));
		$this->view();
	}
}
?>