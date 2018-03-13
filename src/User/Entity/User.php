<?php

namespace App\User\Entity;

use App\Common\Exception\ValidationException;
use App\User\Entity\Security\Credential;
use App\User\Event\UserWasCreated;
use BornFree\TacticianDomainEvent\Recorder\ContainsRecordedEvents;
use BornFree\TacticianDomainEvent\Recorder\EventRecorderCapabilities;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements ContainsRecordedEvents
{
    use EventRecorderCapabilities;

    const ROLE_USER = 'ROLE_USER';

    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @var FullName
     * @ORM\Embedded(class="App\User\Entity\FullName", columnPrefix=false)
     */
    private $fullName;

    /**
     * @var Credential
     * @ORM\OneToOne(targetEntity="App\User\Entity\Security\Credential", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="id")
     */
    private $credential;

    public function __construct(UserBuilder $builder)
    {
        $this->id = Uuid::uuid4();
        $this->fullName = $builder->fullName();
        $this->credential = new Credential($this->id, $builder->email(), $builder->password(), $this->roles());

        $this->record(new UserWasCreated());
    }

    public static function builder(): UserBuilder
    {
        return new UserBuilder();
    }

    public function roles(): array
    {
        return [ self::ROLE_USER ];
    }

    /**
     * Validate user
     *
     * @param ValidatorInterface $validator
     * @throws ValidationException
     */
    public function validate(ValidatorInterface $validator): void
    {
        $errors = $validator->validate($this);
        $errors->addAll($validator->validate($this->credential));

        if (0 !== count($errors)) {
            throw new ValidationException($errors);
        }
    }
}