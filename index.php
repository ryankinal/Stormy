<?php
include_once('lib/config.php');
include_once('lib/Renderer.php');
include_once('classes/User.php');
include_once('classes/Files.php');
include_once('classes/Shares.php');
include_once('classes/FileController.php');

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
                $controller = new FileController();
                
                if ($mod && $mod === 'json')
                {
                    $controller->json($user);
                    exit();
                }
                else if ($mod && $mod === 'detail')
                {
                    $controller->detail($user);
                    exit();
                }
                else if ($mod && $mod === 'share')
                {
                    $controller->share($user);
                    exit();
                }
                else if ($mod && $mod === 'delete')
                {
                    $controller->delete($user);
                    exit();
                }
                else if ($mod && $mod === 'upload')
                {
                    $controller->upload($user);
                    exit();
                }
                else if ($mod && $mod === 'download')
                {
                    $controller->download($user);
                    exit();
                }
                else
                {
                    $controller->index($user);
                    exit();
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