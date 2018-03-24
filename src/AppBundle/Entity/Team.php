<?php

//Class to hold data about win / draw points, number of fixtures, table sort order etc
namespace AppBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="team")
 */
class Team {
    
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;
    
    /**
    * @ORM\Column(name="name", type="string")
    * @Assert\NotBlank(groups={"update"})
    */
    private $name; 
    
    /**
    * @ORM\Column(name="badge_image_url", type="string", nullable=true)
    * @Assert\File(mimeTypes={ "image/png" }, groups={"Team"}, mimeTypesMessage="Please upload a PNG file")
    */
    private $badgeImage;
    
    /**
    * @ORM\Column(name="twitter_url", type="string", nullable=true)
    */
    private $twitter_url; 
    
    /**
    * @ORM\Column(name="website_url", type="string", nullable=true)
    */
    private $website_url;
    
    /**
    * @ORM\Column(name="nickname", type="string", nullable=true)
    */
    private $nickname;
    
    /**
    * @ORM\Column(name="display_name", type="string", nullable=true)
    */
    private $displayName;

    /**
     * @ORM\ManyToOne(targetEntity="League", inversedBy="teams", fetch="EAGER")
     * @ORM\JoinColumn(name="league_id", referencedColumnName="id")
     */
    private $league;

    /**
     * @ORM\Column(name="colour_one", type="string", nullable=true)
     */
    private $colourOne;

    /**
     * @ORM\Column(name="colour_two", type="string", nullable=true)
     */
    private $colourTwo;

    /**
     * @ORM\Column(name="colour_three", type="string", nullable=true)
     */
    private $ColourThree;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Player", mappedBy="team")
     */
    private $players;

    public function __construct() {
        $this->players = new ArrayCollection();
    }

    public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}

	public function getBadgeImage(){
		return $this->badgeImage;
	}

	public function setBadgeImage($badgeImage){
		$this->badgeImage = $badgeImage;
	}

	public function getTwitterUrl(){
		return $this->twitter_url;
	}

	public function setTwitterUrl($twitter_url){
		$this->twitter_url = $twitter_url;
	}

	public function getWebsiteUrl(){
		return $this->website_url;
	}

	public function setWebsiteUrl($website_url){
		$this->website_url = $website_url;
	}

	public function getNickname(){
		return $this->nickname;
	}

	public function setNickname($nickname){
		$this->nickname = $nickname;
	}

    //Print the display name of a team
    //For example Heart of Midlothian are often referred to as Hearts
    //But their nickname is the Jambos
	public function getDisplayName(){
        
        if ($this->displayName == null) {
            return $this->name;
        }
        
		return $this->displayName;
	}

	public function setDisplayName($displayName){
		$this->displayName = $displayName;
	}

    public function getLeague()
    {
        return $this->league;
    }

    public function setLeague($league)
    {
        $this->league = $league;
    }

    public function getColourOne()
    {
        return $this->colourOne;
    }

    public function setColourOne($colourOne)
    {
        $this->colourOne = $colourOne;
    }

    public function getColourTwo()
    {
        return $this->colourTwo;
    }

    public function setColourTwo($colourTwo)
    {
        $this->colourTwo = $colourTwo;
    }

    public function getColourThree()
    {
        return $this->ColourThree;
    }

    public function setColourThree($ColourThree)
    {
        $this->ColourThree = $ColourThree;
    }

    public function getPlayers()
    {
        return $this->players;
    }

    public function setPlayers($players)
    {
        $this->players = $players;
    }

}