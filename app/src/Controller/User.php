<?php

namespace Test\Controller;

use Test\lib\Controller;
use Test\lib\Request;
use Test\lib\Session;
use Test\lib\Translator;
use Test\Model\UserModel;

class User extends Controller
{
    /**
     * @param Request $request
     *
     * @return string
     */
    public function loginAction(Request $request)
    {
        if ($this->get('session')->isUserLogged()) {
            if ($referer = $request->getReferer()) {
                $this->redirectToUrl($referer);
            }

            return $this->redirect('app.index');
        }

        $error = null;
        $email = null;
        if ($request->isPost()) {
            if ($request->requestParam('csrfToken') !== $this->get('session')->getcsrfToken()) {
                $this->get('session')->refreshCsrfToken();

                return $this->render('login.phtml', ['error' => $this->translate('Bad request.')]);
            }
            $email = $request->requestParam('email');
            $password = $request->requestParam('password');
            if ($email and $password) {
                $model = new UserModel();
                /** @var Session $session */
                $session = $this->get('session');
                if ($user = $model->findOneBy(['email' => $email])) {
                    if ($session->checkPassword($password, $user->password)) {
                        $session->setUser($user);

                        return $this->redirect('app.index');
                    }

                    $error = $this->translate('Entered password is wrong.');
                }

                $error = $error ?: $this->translate('Entered email is not found.');
            }

            $error = $error ?: $this->translate('Entered data is not complete.');
        } else {
            if ($lang = $request->get('lang') and \in_array($lang, Translator::AVAILABLE_LANGS)) {
                $this->get('translator')->setLang($lang);
            }
        }

        return $this->render('login.phtml', ['error' => $error, 'email' => $email]);
    }

    /**
     * @return string
     */
    public function logoutAction()
    {
        $this->get('session')->unSetUser();

        return $this->redirect('app.index');
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function registerAction(Request $request)
    {
        if ($this->get('session')->isUserLogged()) {
            if ($referer = $request->getReferer()) {
                $this->redirectToUrl($referer);
            }

            return $this->redirect('app.index');
        }
        if ($lang = $request->get('lang') and \in_array($lang, Translator::AVAILABLE_LANGS)) {
            $this->get('translator')->setLang($lang);
        }

        $error = null;
        if ($request->isPost()) {
            if ($request->requestParam('csrfToken') !== $this->get('session')->getcsrfToken()) {
                $this->get('session')->refreshCsrfToken();

                return $this->render('register.phtml', ['error' => $this->translate('Bad request.')]);
            }
            $email = $request->requestParam('email');
            $model = new UserModel();
            if ($user = $model->findBy(['email' => $email])) {
                $error = $this->translate('Sorry, specified email already used');

                return $this->render('register.phtml', \array_merge(['error' => $error], $request->request()));
            }
            $password = $request->requestParam('password');
            $name = $request->requestParam('name');
            $lang = $request->requestParam('lang');
            $imagePath = sprintf('%s/%s', $this->get('app')->getImagesPath(), 'nouser.svg');
            if ($fileName = $request->files()['file_avatar']['name']) {
                $output = \shell_exec(sprintf('file -i "%s"', $request->files()['file_avatar']['tmp_name']));
                if (\preg_match('~image/(png|jpg|jpeg|gif)~m', $output, $matches)) {
                    if ($request->files()['file_avatar']['error'] !== 0) {
                        $error = $this->translate('Error upload file');
                    } else {
                        $newFileName = sprintf(
                            '%s.%s',
                            md5(
                                file_get_contents($request->files()['file_avatar']['tmp_name'])
                            ),
                            $matches[1]
                        );
                        $dest = $this->get('app')->getImagesDir();
                        \move_uploaded_file($request->files()['file_avatar']['tmp_name'], \sprintf('%s/%s', $dest, $newFileName));
                        $imagePath = sprintf('%s/%s', $this->get('app')->getImagesPath(), $newFileName);
                    }
                } else {
                    $error = $this->translate('Unsupported image type');

                    return $this->render('register.phtml', \array_merge(['error' => $error], $request->request()));
                }
            }
            $user = new \stdClass();
            $user->email = $email;
            $user->name = $name;
            $user->lang = $lang;
            $user->password = \password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            $user->profile_image = $imagePath;
            $model->store($user);
            $this->get('session')->setUser($model->get());
            $this->redirect('app.index');
        }

        return $this->render('register.phtml', \array_merge(['error' => $error], $request->request()));
    }
}