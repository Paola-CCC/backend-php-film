<?php


declare(strict_types=1);

namespace App\Entity;

class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $password;


    //GETTER
    public function getId(): int
    {
        return $this->id;
    }
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    //SETTER
    public function setId(int $id)
    {
        $this->id = $id;
        return $this->id;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
        return $this->username;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this->email;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this->password;
    }
}

