<?php
include_once('classes/Files.php');
include_once('lib/Controller.php');

class FileController extends Controller
{
    public function json($user)
    {
        header('Content-type: application/json');
        $files = new Files();
        $files->orderBy('created DESC');
        $files->getBy(array('_user_id' => $user->getAttributes('user_id')));
        echo $files->getJSON();
        return true;        
    }

    public function delete($user)
    {
        header('Content-type: application/json');
        $token = isset($_POST['token']) ? $_POST['token'] : false;
        $id = isset($_POST['id']) ? $_POST['id'] : false;

        if ($id && $token === $_SESSION['token'])
        {
            $file = new File(array(
                'file_id' => $id,
                '_user_id' => $user->getAttribute('user_id')
            ));

            if ($file->loadByAttributes())
            {
                if ($file->delete())
                {
                    echo json_encode(array('deleted' => $file->getAttributes()));
                    return true;
                }
            }
        }

        echo json_encode(array('error' => 'Could not delete that file'));
        return false;
    }

    public function upload($user)
    {
        $token = isset($_POST['token']) ? $_POST['token'] : false;
        $target = isset($_FILES['file']) ? $_FILES['file'] : false;

        if ($token === $_SESSION['token'] && $target)
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
                        echo json_encode(array('update' => $file->getAttributes()));
                    }
                    else
                    {
                        echo $file->getJSON();
                    }

                    return true;
                }
            }
        }

        return false;
    }

    public function download($user)
    {
        $id = isset($_GET['id']) ? $_GET['id'] : false;

        if ($id)
        {
            $file = new File(array(
                'file_id' => $id,
                '_user_id' => $user->getAttribute('user_id')
            ));

            if ($file->loadByAttributes())
            {
                header('Cache-control: public');
                header('Content-Description: File Transfer');
                header('Content-Disposition: attachment; filename='.$file->getAttribute('name'));
                header('Content-Type: '.$file->getAttribute('mime_type'));
                header('Content-Transfer-Encoding: binary');

                readfile($file->getAttribute('location'));
                return true;
            }
        }

        exit();
    }

    public function index($user)
    {
        $files = new Files();
        $files->orderBy('created DESC');
        $files->getBy(array('user_id' => $user->getAttribute('user_id')));

        $renderer = new Renderer('layouts/overall.tpl');
        $renderer->setTitle('Your Stuff');
        $renderer->setKeywords(array(
            'your',
            'stuff',
            'here'
        ));
        $renderer->setNav('nav/dashboard.tpl');
        $renderer->addScript('scripts/upload.js');
        $renderer->addScript('scripts/json_parse.js');
        $renderer->addContent('dashboard.tpl', array(
            'files' => $files->getElements(),
            'token' => $_SESSION['token'],
            'filesCount' => count($files->getElements())
        ));
        $renderer->render();
    }

    public function detail($user)
    {
        $id = isset($_GET['id']) ? $_GET['id'] : false;

        if ($id)
        {
            $file = new File(array(
                'file_id' => $id,
                '_user_id' => $user->getAttribute('user_id')
            ));

            if ($file->loadByAttributes())
            {
                $renderer = new Renderer('layouts/overall.tpl');
                $renderer->setTitle('File Detail: '.$file->getAttribute('file_name'));
                $renderer->setKeywords(array(
                    'your',
                    'stuff',
                    'here'
                ));
                $renderer->setNav('nav/dashboard.tpl');
                $renderer->addContent('file-detail.tpl', array(
                    'phile' => $file,
                    'token' => $_SESSION['token']
                ));
                $renderer->render();

                return true;
            }
        }

        return false;
    }

    public function share($user)
    {
        return true;
    }
}
?>