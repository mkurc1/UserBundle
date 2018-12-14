# UserBundle

Symfony User Bundle. The bundle allow in easy way to manage user and security in Symfony 4.2.

## Configure

Require the bundle with composer:

    $ composer require mkurc1/user-bundle

Add vendor into config/bundles.php:

    <?php
    // app/bundles.php

    return [
        // ...
        UserBundle\UserBundle::class => ['all' => true],
        // ...
    ];
    
Create your User class:
    
    <?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use UserBundle\Entity\User as BaseUser;
    
    /**
     * @ORM\Entity()
     */
    class User extends BaseUser
    {
    
Create file config/packages/user.yaml:

    user:
        user_class: App\Entity\User
        login:
            default_target_path: dashboard # default router after login
        
Add into config/routes.yaml:

    logout:
        path: /logout
        
Add into config/routes/annotations.yaml:

    securiry:
        resource: ../../vendor/mkurc1/user-bundle/Controller/
        type: annotation

Update config/packages/security.yaml:
    
    security:
        encoders:
            # use your user class name here
            App\Entity\User:
                # bcrypt or argon2i are recommended
                # argon2i is more secure, but requires PHP 7.2 or the Sodium extension
                algorithm: bcrypt
                cost: 12
    
        # https://symfony.com/doc/current/security.html#where-do-users-come-from-user-providers
        providers:
            user_provider:
                id: UserBundle\Security\UserProvider
        firewalls:
            dev:
                pattern: ^/(_(profiler|wdt)|css|images|js)/
                security: false
            main:
                anonymous: ~
                form_login:
                    login_path: login
                    check_path: login
                    csrf_token_generator: security.csrf.token_manager
                remember_me:
                    secret:   '%kernel.secret%'
                    lifetime: 2592000 # 4 weeks in seconds
                    path:     /
                logout:
                    path:   /logout
                    target: /
                guard:
                    authenticators:
                        - UserBundle\Security\LoginFormAuthenticator
    
        # Easy way to control access for large sections of your site
        # Note: Only the *first* access control that matches will be used
        access_control:
            - { path: ^/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/register, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/confirm, roles: IS_AUTHENTICATED_ANONYMOUSLY }
            - { path: ^/, roles: IS_AUTHENTICATED_REMEMBERED }

                
Update your database schema:

    $ php bin/console doctrine:schema:update --force
    
## Commands

You have access to several commands:

    $ php bin/console user:create # Create a user
    $ php bin/console user:activate # Activate a user
    $ php bin/console user:change-password # Change the password of a user
    $ php bin/console user:deactivate # Deactivate a user
    $ php bin/console user:demote # Demote a user by removing a role
    $ php bin/console user:promote # Promote a user by adding a role

## License

The bundle is released under the [MIT License](LICENSE).
