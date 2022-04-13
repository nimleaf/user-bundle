<?php declare(strict_types=1);
namespace Imatic\Bundle\UserBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Imatic\Bundle\UserBundle\Model\GroupInterface;
use Imatic\Bundle\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User.
 *
 * @author Viliam Husár <viliam.husar@imatic.cz>
 *
 * @ORM\MappedSuperclass()
 * @DoctrineAssert\UniqueEntity(fields="usernameCanonical", errorPath="username", groups={"Registration", "Profile"})
 * @DoctrineAssert\UniqueEntity(fields="emailCanonical", errorPath="email", groups={"Registration", "Profile"})
 */
class User implements UserInterface, \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected int $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"Registration", "Profile"})
     * @Assert\Length(min=2, max=255, groups={"Registration", "Profile"})
     */
    protected string $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected string $usernameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"Registration", "Profile"})
     * @Assert\Length(min=2, max=254, groups={"Registration", "Profile"})
     * @Assert\Email(groups={"Registration", "Profile"})
     */
    protected string $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected string $emailCanonical;

    /**
     * Encrypted password.
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected string $password;

    /**
     * Plain password, Used for model validation, must not be persisted.
     *
     * @var string
     *
     * @Assert\NotBlank(groups={"Registration", "ResetPassword", "ChangePassword"})
     * @Assert\Length(min=2, groups={"Registration", "Profile", "ResetPassword", "ChangePassword"})
     */
    protected string $plainPassword;

    /**
     * The salt to use for hashing.
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected string $salt;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $confirmationToken;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $passwordRequestedAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected bool $enabled;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected bool $locked;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected bool $expired;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $expiresAt;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected bool $credentialsExpired;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $credentialsExpireAt;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected array $roles;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Imatic\Bundle\UserBundle\Model\GroupInterface", cascade={"persist"})
     */
    protected Collection $groups;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->salt = \base_convert(\sha1(\uniqid((string) \mt_rand(), true)), 16, 36);
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->credentialsExpired = false;
        $this->roles = [];
        $this->groups = new ArrayCollection();
    }

    /**
     * Returns string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getUsername();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets username.
     *
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username):User
    {
        $this->username = (string) $username;

        return $this;
    }

    /**
     * Returns username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Sets canonical username.
     *
     * @param string $usernameCanonical
     *
     * @return $this
     */
    public function setUsernameCanonical($usernameCanonical): User
    {
        $this->usernameCanonical = (string) $usernameCanonical;

        return $this;
    }

    /**
     * Returns canonical username.
     *
     * @return string
     */
    public function getUsernameCanonical(): string
    {
        return $this->usernameCanonical;
    }

    /**
     * Sets email.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email): User
    {
        $this->email = (string) $email;

        return $this;
    }

    /**
     * Returns email.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets canonical email.
     *
     * @param string $emailCanonical
     *
     * @return $this
     */
    public function setEmailCanonical($emailCanonical): User
    {
        $this->emailCanonical = (string) $emailCanonical;

        return $this;
    }

    /**
     * Returns canonical email.
     *
     * @return string
     */
    public function getEmailCanonical(): string
    {
        return $this->emailCanonical;
    }

    /**
     * Sets encrypted password.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password): User
    {
        $this->password = (string) $password;

        return $this;
    }

    /**
     * Returns encrypted password.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Sets plain password.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPlainPassword($password): User
    {
        $this->plainPassword = (string) $password;

        return $this;
    }

    /**
     * Returns plain password.
     *
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $salt
     *
     * @return $this
     */
    public function setSalt($salt): User
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Returns salt.
     *
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * Sets last login.
     *
     * @param DateTime $time
     *
     * @return $this
     */
    public function setLastLogin(DateTime $time = null): User
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * Returns last login time.
     *
     * @return DateTime
     */
    public function getLastLogin():DateTime
    {
        return $this->lastLogin;
    }

    /**
     * Sets confirmation token.
     *
     * @param string $confirmationToken
     *
     * @return $this
     */
    public function setConfirmationToken($confirmationToken): User
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Returns confirmation token.
     *
     * @return string
     */
    public function getConfirmationToken(): string
    {
        return $this->confirmationToken;
    }

    /**
     * Sets password request at.
     *
     * @param DateTime $date
     *
     * @return $this
     */
    public function setPasswordRequestedAt(DateTime $date = null): User
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Returns password requested at.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt(): ?DateTime
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Sets enabled.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setEnabled($bool): User
    {
        $this->enabled = (bool) $bool;

        return $this;
    }

    /**
     * Returns true if user is enabled, false otherwise.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Sets locked.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setLocked($bool): User
    {
        $this->locked = (bool) $bool;

        return $this;
    }

    /**
     * Returns true if user is locked, false otherwise.
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return !$this->isAccountNonLocked();
    }

    /**
     * Sets expired.
     *
     * @param bool $bool
     *
     * @return User
     */
    public function setExpired($bool): User
    {
        $this->expired = (bool) $bool;

        return $this;
    }

    /**
     * Returns true if user is expired, false otherwise.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return !$this->isAccountNonExpired();
    }

    /**
     * Sets expired at.
     *
     * @param DateTime $date
     *
     * @return $this
     */
    public function setExpiresAt(DateTime $date = null): User
    {
        $this->expiresAt = $date;

        return $this;
    }

    /**
     * Sets credential expired at.
     *
     * @param DateTime $date
     *
     * @return $this
     */
    public function setCredentialsExpireAt(DateTime $date = null): User
    {
        $this->credentialsExpireAt = $date;

        return $this;
    }

    /**
     * Sets credentials expired.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setCredentialsExpired($bool): User
    {
        $this->credentialsExpired = (bool) $bool;

        return $this;
    }

    /**
     * Sets roles.
     *
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): User
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Adds role.
     *
     * @param string $role
     *
     * @return $this
     */
    public function addRole($role): User
    {
        $role = (string) $role;
        $role = \strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Removes role.
     *
     * @param string $role
     *
     * @return $this
     */
    public function removeRole($role): User
    {
        $role = (string) $role;

        if (false !== $key = \array_search(\strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = \array_values($this->roles);
        }

        return $this;
    }

    /**
     * Returns roles.
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = \array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return \array_unique($roles);
    }

    /**
     * Returns true if user has role.
     *
     * Never use this to check if this user has access to anything!
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role): bool
    {
        $role = (string) $role;

        return \in_array(\strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Adds group.
     *
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function addGroup(GroupInterface $group): User
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * Removes group.
     *
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function removeGroup(GroupInterface $group): User
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    /**
     * Returns groups.
     *
     * @return Collection|GroupInterface[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * Returns group names.
     *
     * @return array
     */
    public function getGroupNames(): array
    {
        $names = [];
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * Returns true if user has given group, false otherwise.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasGroup($name): bool
    {
        return \in_array($name, $this->getGroupNames(), true);
    }

    /**
     * Sets super admin.
     *
     * @param bool $bool
     *
     * @return $this
     */
    public function setSuperAdmin($bool): User
    {
        $bool = (bool) $bool;

        if (true === $bool) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * Returns true if user is super admin, false otherwise.
     *
     * @return bool
     */
    public function isSuperAdmin():bool
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * Returns true if user account is not expired, false otherwsie.
     *
     * @return bool
     */
    public function isAccountNonExpired():bool
    {
        if (true === $this->expired) {
            return false;
        }

        if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < \time()) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if account is not locked, false otherwise.
     *
     * @return bool
     */
    public function isAccountNonLocked():bool
    {
        return !$this->locked;
    }

    /**
     * Returns true if user credentials are not expired, false otherwise.
     *
     * @return bool
     */
    public function isCredentialsNonExpired():bool
    {
        if (true === $this->credentialsExpired) {
            return false;
        }

        if (null !== $this->credentialsExpireAt && $this->credentialsExpireAt->getTimestamp() < \time()) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if user credentials expired, false otherwise.
     *
     * @return bool
     */
    public function isCredentialsExpired():bool
    {
        return !$this->isCredentialsNonExpired();
    }

    /**
     * Returns true if password request is not expired.
     *
     * @param int $ttl
     *
     * @return bool
     */
    public function isPasswordRequestNonExpired($ttl):bool
    {
        return $this->getPasswordRequestedAt() instanceof DateTime &&
        $this->getPasswordRequestedAt()->getTimestamp() + $ttl > \time();
    }

    /**
     * Returns true if same user, false otherwise.
     *
     * @return bool
     */
    public function isUser(UserInterface $user = null):bool
    {
        return null !== $user && $this->getId() === $user->getId();
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize():string
    {
        return \serialize([
                $this->password,
                $this->salt,
                $this->usernameCanonical,
                $this->username,
                $this->expired,
                $this->locked,
                $this->credentialsExpired,
                $this->enabled,
                $this->id,
            ]);
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $data = \unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = \array_merge($data, \array_fill(0, 2, null));

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id
            ) = $data;
    }
}
