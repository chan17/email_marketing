<?php
namespace Acme\Service\User;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class CurrentUser implements AdvancedUserInterface, EquatableInterface, \ArrayAccess  {

    protected $data;

    public function __set($name, $value) {
        if (array_key_exists($name, $this->data)) {
            $this->data[$name] = $value;
        }
        throw new \RuntimeException("{$name} is not exist in CurrentUser.");
    }

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        throw new \RuntimeException("{$name} is not exist in CurrentUser.");
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function __unset($name) {
        unset($this->data[$name]);
    }

    public function offsetExists ($offset) {
        return $this->__isset($offset);

    }
    public function offsetGet ($offset) {
        return $this->__get($offset);
    }

    public function offsetSet ($offset, $value) {
        return $this->__set($offset, $value);
    }

    public function offsetUnset ($offset) {
        return $this->__unset($offset);
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getUsername() {
        return $this->username;
    }

    public function eraseCredentials() {

    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return !$this->locked;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return true;
    }

    public function isEqualTo(UserInterface $user) {
        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function fromArray(array $user) {
        $this->data = $user;
        return $this;
    }

    public function toArray() {
        return $this->data;
    }
}