<?php

namespace App\Entity;

use App\Service\StateEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=5)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="dateCloture",
     *     message="La valeur doit être supérieure à la date de clôture"
     * )
     * @Assert\NotBlank
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $duration;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\LessThanOrEqual(
     *     propertyPath="dateDebut",
     *     message="La valeur doit être inférieure à la date de début"
     *     )
     * @Assert\NotBlank
     */
    private $dateCloture;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Range(min="5", minMessage="Vous devez acceptes un minimum de {{ limit }} inscriptions")
     */
    private $inscriptionsMax;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pictureUrl;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $lieu;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisator;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subscription", mappedBy="event")
     */
    private $subscriptions;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motif;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->setState(StateEnum::STATE_CREATE);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->dateCloture;
    }

    public function setDateCloture(\DateTimeInterface $dateCloture): self
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    public function getInscriptionsMax(): ?int
    {
        return $this->inscriptionsMax;
    }

    public function setInscriptionsMax(int $inscriptionsMax): self
    {
        $this->inscriptionsMax = $inscriptionsMax;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPictureUrl(): ?string
    {
        return $this->pictureUrl;
    }

    public function setPictureUrl(?string $pictureUrl): self
    {
        $this->pictureUrl = $pictureUrl;

        return $this;
    }

    public function getLieu(): ?Location
    {
        return $this->lieu;
    }

    public function setLieu(?Location $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getOrganisator(): ?User
    {
        return $this->organisator;
    }

    public function setOrganisator(?User $organisator): self
    {
        $this->organisator = $organisator;

        return $this;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setEvent($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
            // set the owning side to null (unless already changed)
            if ($subscription->getEvent() === $this) {
                $subscription->setEvent(null);
            }
        }

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }
}
