<?php

namespace App\Controller;

use App\ReadModel\User\UserFetcher;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Model\User\UseCase\Reset;
use App\Model\User\UseCase\SignUp;

class AuthController extends AbstractController
{
    private $users;
    private $errors;

    public function __construct(UserFetcher $users, ErrorHandler $errors)
    {
        $this->users = $users;
        $this->errors = $errors;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws \Exception
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Will be intercepted before getting here');
    }

    /**
     * @Route("/signup", name="auth.signup.request")
     * @param Request $request
     * @param SignUp\Request\Handler $handler
     * @return Response
     */
    public function signUpRequest(Request $request, SignUp\Request\Handler $handler): Response
    {
        $command = new SignUp\Request\Command();

        $form = $this->createForm(SignUp\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Check your email and follow the confirmation link.');
                return $this->redirectToRoute('app_login');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/signup/{token}", name="auth.signup.confirm")
     * @param Request $request
     * @param string $token
     * @param SignUp\Confirm\Handler $handler
     * @param UserProviderInterface $userProvider
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return Response
     */
    public function signUpConfirm(
        Request $request,
        string $token,
        SignUp\Confirm\Handler $handler,
        UserProviderInterface $userProvider,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ): Response
    {
        if (!$user = $this->users->findBySignUpConfirmToken($token)) {
            $this->addFlash('error', 'Incorrect or already confirmed token.');
            return $this->redirectToRoute('app_login');
        }

        $command = new SignUp\Confirm\Command($token);

        try {
            $handler->handle($command);
            return $guardHandler->authenticateUserAndHandleSuccess(
                $userProvider->loadUserByUsername($user->email),
                $request,
                $authenticator,
                'main'
            );
        } catch (\DomainException $e) {
            $this->errors->handle($e);
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/reset", name="auth.reset.request")
     * @param Request $request
     * @param Reset\Request\Handler $handler
     * @return Response
     */
    public function resetRequest(Request $request, Reset\Request\Handler $handler): Response
    {
        $command = new Reset\Request\Command();

        $form = $this->createForm(Reset\Request\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password reset link was sent. Check your email.');
                return $this->redirectToRoute('app_login');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('auth/reset/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset/{token}", name="auth.reset.change")
     * @param string $token
     * @param Request $request
     * @param Reset\Reset\Handler $handler
     * @return Response
     */
    public function resetChange(string $token, Request $request, Reset\Reset\Handler $handler): Response
    {
        if (!$this->users->hasByResetToken($token)) {
            $this->addFlash('error', 'Incorrect reset token.');
            return $this->redirectToRoute('app_login');
        }

        $command = new Reset\Reset\Command($token);

        $form = $this->createForm(Reset\Reset\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Password was successfully changed. You can use it to log in.');
                return $this->redirectToRoute('app_login');
            } catch (\DomainException $e) {
                $this->errors->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('auth/reset/change.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
