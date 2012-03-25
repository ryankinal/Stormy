<?php
include_once('lib/config.php');
include_once('classes/Shares.php');
include_once('classes/FileController.php');
include_once('classes/AccountController.php');
include_once('classes/ContentController.php');

$user = new User();
$hasUser = $user->loadCurrent();

$path = isset($_GET['path']) ? $_GET['path'] : false;
$mod = isset($_GET['mod']) ? $_GET['mod'] : false;
$token = isset($_POST['token']) ? $_POST['token'] : false;

if ($path)
{
    if ($hasUser)
    {
        switch ($path)
        {
            case 'account':
                $controller = new AccountController();

                if ($mod)
                {
                    if ($mod === 'update')
                    {
                        $controller->update($user);
                        exit();
                    }
                    else if ($mod === 'delete')
                    {
                        $controller->delete($user);
                        exit();
                    }
                }

                $controller->index($user);
                exit();
                break;
            case 'files':
                $controller = new FileController();
                
                if ($mod)
                {
                    if ($mod === 'json')
                    {
                        $controller->json($user);
                        exit();
                    }
                    else if ($mod === 'detail')
                    {
                        $controller->detail($user);
                        exit();
                    }
                    else if ($mod === 'share')
                    {
                        $controller->share($user);
                        exit();
                    }
                    else if ($mod === 'delete')
                    {
                        $controller->delete($user);
                        exit();
                    }
                    else if ($mod === 'upload')
                    {
                        $controller->upload($user);
                        exit();
                    }
                    else if ($mod === 'download')
                    {
                        $controller->download($user);
                        exit();
                    }
                }

                $controller->index($user);
                exit();

                break;
            case 'logout':
                $controller = new AccountController();
                $controller->logout($user);
                break;
            case 'about':
                $controller = new ContentController();
                $controller->about();
                break;
            default:
                header('Location: '.WEB_ROOT.'/files/');
                exit();
        }
    }
    else
    {
        switch ($path)
        {
            case 'signup':
                $controller = new AccountController();
                $controller->create();
                break;
            case 'login':
                $controller = new AccountController();
                $controller->login();

                break;
            case 'about':
                $controller = new ContentController();
                $controller->about();
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
        $controller = new ContentController();
        $controller->index();
    }
}
?>