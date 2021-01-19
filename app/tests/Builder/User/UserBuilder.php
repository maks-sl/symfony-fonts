<?php

declare(strict_types=1);

namespace App\Tests\Builder\User;

use App\Model\User\Entity\Email;
use App\Model\User\Entity\Name;
use App\Model\User\Entity\User;
use App\Model\User\Entity\Id;

class UserBuilder
{
    private $id;
    private $email;
    private $hash;
    private $date;
    private $name;
    private $token;

    private $confirmed;

    private $role;

    public function __construct()
    {
        $this->id = Id::next();
        $this->email = new Email('mail@app.test');
        $this->hash = 'hash';
        $this->date = new \DateTimeImmutable();
        $this->name = new Name('First', 'Last');
        $this->token = 'token';
    }

    public function withId(Id $id): self
    {
        $clone = clone $this;
        $clone->id = $id;
        return $clone;
    }

    public function withCredentials(Email $email = null, string $hash = null): self
    {
        $clone = clone $this;
        if ($email) {
            $clone->email = $email;
        }
        if ($hash) {
            $clone->hash = $hash;
        }
        return $clone;
    }

    public function withName(Name $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function confirmed(): self
    {
        $clone = clone $this;
        $clone->confirmed = true;
        return $clone;
    }

    public function build(): User
    {
        $user = User::signUpRequest(
            $this->id,
            $this->date,
            $this->email,
            $this->hash,
            $this->name,
            $this->token
        );

        if ($this->confirmed) {
            $user->signUpConfirm();
        }

        return $user;
    }
}
