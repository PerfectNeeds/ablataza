<?php

namespace MD\Bundle\UserBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use MD\Bundle\MediaBundle\Entity\Image as Image;

/**
 * Person
 *
 * @ORM\Table("person")
 * @ORM\Entity
 */
class Person {

    // Child Type 

    const CHILD = 1;
    // User Type
    const REGULAR = 1;
    const MAIN = 2;
    // metonymy types 
    const TEACHER = 1;
    const DOCTOR = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\Seo", inversedBy="person",cascade={"persist"})
     */
    protected $seo;

    /**
     * @Assert\NotBlank()

     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true ,  length=255)
     */
    private $familyname;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     * )
     * @ORM\Column(name="email", type="string", length=225)
     */
    private $email;

    /**
     * @ORM\Column(name="address", nullable=true , type="string", length=255)
     */
    private $address;

    /**
     *
     * @ORM\Column(name="child", type="boolean")
     */
    private $child = false;

    /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=25 ,nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(name="birthdate",nullable=true , type="date" )
     */
    private $birthdate;

    /**
     *
     * @ORM\Column(name="gender", type="boolean", nullable = true)
     */
    private $gender;

    /**
     * @ORM\OneToMany(targetEntity="\MD\Bundle\CMSBundle\Entity\Recipe", mappedBy="person")
     */
    protected $recipes;

    /**
     * @ORM\OneToMany(targetEntity="\MD\Bundle\CMSBundle\Entity\Article", mappedBy="person")
     */
    protected $articles;

    /**
     * @ORM\OneToMany(targetEntity="\MD\Bundle\CMSBundle\Entity\SurveyAnswer", mappedBy="person")
     */
    protected $surveyAnswers;

    /**
     * @ORM\OneToMany(targetEntity="\MD\Bundle\CMSBundle\Entity\Tip", mappedBy="person")
     */
    protected $tips;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\ArticleFavorite", mappedBy="person")
     */
    protected $articleFavorite;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\FadfadaFavorite", mappedBy="person")
     */
    protected $fadfadaFavorite;

    /**
     * @ORM\OneToOne(targetEntity="\MD\Bundle\CMSBundle\Entity\RecipeFavorite", mappedBy="person")
     */
    protected $recipeFavorite;

    /**
     * @ORM\OneToMany(targetEntity="Account", mappedBy="person")
     */
    protected $accounts;

    /**
     * @ORM\ManyToMany(targetEntity="\MD\Bundle\MediaBundle\Entity\Image")
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="Follower", mappedBy="follower")
     */
    protected $followers;

    /**
     * @ORM\OneToMany(targetEntity="Follower", mappedBy="person")
     */
    protected $followerPersons;

    /**
     * @ORM\OneToMany(targetEntity="MenuPlanner", mappedBy="person")
     */
    protected $menuPlanners;

    /**
     * @ORM\OneToMany(targetEntity="SuperMarket", mappedBy="person")
     */
    protected $superMarkets;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     * @return Person
     */
    public function setBirthdate($birthdate) {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime 
     */
    public function getBirthdate() {
        return $this->birthdate;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Person
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Set gender
     *
     * @param boolean $gender
     * @return Person
     */
    public function setGender($gender) {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return boolean 
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * Add accounts
     *
     * @param \MD\Bundle\UserBundle\Entity\Account $accounts
     * @return Person
     */
    public function addAccount(\MD\Bundle\UserBundle\Entity\Account $accounts) {
        $this->accounts[] = $accounts;

        return $this;
    }

    /**
     * Remove accounts
     *
     * @param \MD\Bundle\UserBundle\Entity\Account $accounts
     */
    public function removeAccount(\MD\Bundle\UserBundle\Entity\Account $accounts) {
        $this->accounts->removeElement($accounts);
    }

    /**
     * Get accounts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAccounts() {
        return $this->accounts;
    }

    /**
     * Get accounts
     *
     * @return \MD\Bundle\UserBundle\Entity\Account 
     */
    public function getFirstAccount() {
        if (count($this->accounts) > 0) {
            return $this->accounts[0];
        } else {
            return NULL;
        }
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Person
     */
    public function setFirstName($firstName) {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Person
     */
    public function setAddress($address) {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set child
     *
     * @param boolean $child
     * @return Person
     */
    public function setChild($child) {
        $this->child = $child;

        return $this;
    }

    /**
     * Get child
     *
     * @return boolean 
     */
    public function getChild() {
        return $this->child;
    }

    public function __toString() {
        return $this->getFirstName();
    }

    /**
     * Add images
     *
     * @param \MD\Bundle\MediaBundle\Entity\Image $images
     * @return Person
     */
    public function addImage(\MD\Bundle\MediaBundle\Entity\Image $images) {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \MD\Bundle\MediaBundle\Entity\Image $images
     */
    public function removeImage(\MD\Bundle\MediaBundle\Entity\Image $images) {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages($types = FALSE) {
        if ($types) {
            return $this->images->filter(function($image) use ($types) {
                        return in_array($image->getImageType(), $types);
                    });
        } else {
            return $this->images;
        }
    }

    /**
     * Get logoImage
     *
     * @return MD\Bundle\MediaBundle\Entity\Image
     */
    public function getLogoImage() {
        return $this->getImages(array(Image::TYPE_LOGO))->first();
    }

    /**
     * Get passportImage
     *
     * @return MD\Bundle\MediaBundle\Entity\Image
     */
    public function getImage() {
        return $this->getImages(array(Image::TYPE_GALLERY))->first();
    }

    /**
     * Set image
     *
     * @param \MD\Bundle\MediaBundle\Entity\Image $image
     * @return Banner
     */
    public function setImage(\MD\Bundle\MediaBundle\Entity\Image $image = null) {
        $this->images[0] = $image;
        return $this;
    }

    /**
     * Set familyname
     *
     * @param string $familyname
     * @return Person
     */
    public function setFamilyname($familyname) {
        $this->familyname = $familyname;

        return $this;
    }

    /**
     * Get familyname
     *
     * @return string 
     */
    public function getFamilyname() {
        return $this->familyname;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->recipes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->surveyAnswers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tips = new \Doctrine\Common\Collections\ArrayCollection();
        $this->accounts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add recipes
     *
     * @param \MD\Bundle\CMSBundle\Entity\Recipe $recipes
     * @return Person
     */
    public function addRecipe(\MD\Bundle\CMSBundle\Entity\Recipe $recipes) {
        $this->recipes[] = $recipes;

        return $this;
    }

    /**
     * Remove recipes
     *
     * @param \MD\Bundle\CMSBundle\Entity\Recipe $recipes
     */
    public function removeRecipe(\MD\Bundle\CMSBundle\Entity\Recipe $recipes) {
        $this->recipes->removeElement($recipes);
    }

    /**
     * Get recipes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipes() {
        return $this->recipes;
    }

    /**
     * Add articles
     *
     * @param \MD\Bundle\CMSBundle\Entity\Article $articles
     * @return Person
     */
    public function addArticle(\MD\Bundle\CMSBundle\Entity\Article $articles) {
        $this->articles[] = $articles;

        return $this;
    }

    /**
     * Remove articles
     *
     * @param \MD\Bundle\CMSBundle\Entity\Article $articles
     */
    public function removeArticle(\MD\Bundle\CMSBundle\Entity\Article $articles) {
        $this->articles->removeElement($articles);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticles() {
        return $this->articles;
    }

    /**
     * Add surveyAnswers
     *
     * @param \MD\Bundle\CMSBundle\Entity\SurveyAnswer $surveyAnswers
     * @return Person
     */
    public function addSurveyAnswer(\MD\Bundle\CMSBundle\Entity\SurveyAnswer $surveyAnswers) {
        $this->surveyAnswers[] = $surveyAnswers;

        return $this;
    }

    /**
     * Remove surveyAnswers
     *
     * @param \MD\Bundle\CMSBundle\Entity\SurveyAnswer $surveyAnswers
     */
    public function removeSurveyAnswer(\MD\Bundle\CMSBundle\Entity\SurveyAnswer $surveyAnswers) {
        $this->surveyAnswers->removeElement($surveyAnswers);
    }

    /**
     * Get surveyAnswers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSurveyAnswers() {
        return $this->surveyAnswers;
    }

    /**
     * Add tips
     *
     * @param \MD\Bundle\CMSBundle\Entity\Tip $tips
     * @return Person
     */
    public function addTip(\MD\Bundle\CMSBundle\Entity\Tip $tips) {
        $this->tips[] = $tips;

        return $this;
    }

    /**
     * Remove tips
     *
     * @param \MD\Bundle\CMSBundle\Entity\Tip $tips
     */
    public function removeTip(\MD\Bundle\CMSBundle\Entity\Tip $tips) {
        $this->tips->removeElement($tips);
    }

    /**
     * Get tips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTips() {
        return $this->tips;
    }

    /**
     * Set articleFavorite
     *
     * @param \MD\Bundle\CMSBundle\Entity\ArticleFavorite $articleFavorite
     * @return Person
     */
    public function setArticleFavorite(\MD\Bundle\CMSBundle\Entity\ArticleFavorite $articleFavorite = null) {
        $this->articleFavorite = $articleFavorite;

        return $this;
    }

    /**
     * Get articleFavorite
     *
     * @return \MD\Bundle\CMSBundle\Entity\ArticleFavorite 
     */
    public function getArticleFavorite() {
        return $this->articleFavorite;
    }

    /**
     * Set recipeFavorite
     *
     * @param \MD\Bundle\CMSBundle\Entity\RecipeFavorite $recipeFavorite
     * @return Person
     */
    public function setRecipeFavorite(\MD\Bundle\CMSBundle\Entity\RecipeFavorite $recipeFavorite = null) {
        $this->recipeFavorite = $recipeFavorite;

        return $this;
    }

    /**
     * Get recipeFavorite
     *
     * @return \MD\Bundle\CMSBundle\Entity\RecipeFavorite 
     */
    public function getRecipeFavorite() {
        return $this->recipeFavorite;
    }

    /**
     * Set fadfadaFavorite
     *
     * @param \MD\Bundle\CMSBundle\Entity\FadfadaFavorite $fadfadaFavorite
     * @return Person
     */
    public function setFadfadaFavorite(\MD\Bundle\CMSBundle\Entity\FadfadaFavorite $fadfadaFavorite = null) {
        $this->fadfadaFavorite = $fadfadaFavorite;

        return $this;
    }

    /**
     * Get fadfadaFavorite
     *
     * @return \MD\Bundle\CMSBundle\Entity\FadfadaFavorite 
     */
    public function getFadfadaFavorite() {
        return $this->fadfadaFavorite;
    }

    /**
     * Set seo
     *
     * @param \MD\Bundle\CMSBundle\Entity\Seo $seo
     * @return Person
     */
    public function setSeo(\MD\Bundle\CMSBundle\Entity\Seo $seo = null) {
        $this->seo = $seo;

        return $this;
    }

    /**
     * Get seo
     *
     * @return \MD\Bundle\CMSBundle\Entity\Seo 
     */
    public function getSeo() {
        return $this->seo;
    }

    /**
     * Add followers
     *
     * @param \MD\Bundle\UserBundle\Entity\Follower $followers
     * @return Person
     */
    public function addFollower(\MD\Bundle\UserBundle\Entity\Follower $followers) {
        $this->followers[] = $followers;

        return $this;
    }

    /**
     * Remove followers
     *
     * @param \MD\Bundle\UserBundle\Entity\Follower $followers
     */
    public function removeFollower(\MD\Bundle\UserBundle\Entity\Follower $followers) {
        $this->followers->removeElement($followers);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowers() {
        return $this->followers;
    }

    /**
     * Add followerPersons
     *
     * @param \MD\Bundle\UserBundle\Entity\Follower $followerPersons
     * @return Person
     */
    public function addFollowerPerson(\MD\Bundle\UserBundle\Entity\Follower $followerPersons) {
        $this->followerPersons[] = $followerPersons;

        return $this;
    }

    /**
     * Remove followerPersons
     *
     * @param \MD\Bundle\UserBundle\Entity\Follower $followerPersons
     */
    public function removeFollowerPerson(\MD\Bundle\UserBundle\Entity\Follower $followerPersons) {
        $this->followerPersons->removeElement($followerPersons);
    }

    /**
     * Get followerPersons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowerPersons() {
        return $this->followerPersons;
    }

    public function isFollower($personId) {
        foreach ($this->followers as $follower) {
            if ($follower->getPerson()->getId() == $personId) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Add menuPlanners
     *
     * @param \MD\Bundle\UserBundle\Entity\MenuPlanner $menuPlanners
     * @return Person
     */
    public function addMenuPlanner(\MD\Bundle\UserBundle\Entity\MenuPlanner $menuPlanners) {
        $this->menuPlanners[] = $menuPlanners;

        return $this;
    }

    /**
     * Remove menuPlanners
     *
     * @param \MD\Bundle\UserBundle\Entity\MenuPlanner $menuPlanners
     */
    public function removeMenuPlanner(\MD\Bundle\UserBundle\Entity\MenuPlanner $menuPlanners) {
        $this->menuPlanners->removeElement($menuPlanners);
    }

    /**
     * Get menuPlanners
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenuPlanners() {
        return $this->menuPlanners;
    }


    /**
     * Add superMarkets
     *
     * @param \MD\Bundle\UserBundle\Entity\SuperMarket $superMarkets
     * @return Person
     */
    public function addSuperMarket(\MD\Bundle\UserBundle\Entity\SuperMarket $superMarkets)
    {
        $this->superMarkets[] = $superMarkets;
    
        return $this;
    }

    /**
     * Remove superMarkets
     *
     * @param \MD\Bundle\UserBundle\Entity\SuperMarket $superMarkets
     */
    public function removeSuperMarket(\MD\Bundle\UserBundle\Entity\SuperMarket $superMarkets)
    {
        $this->superMarkets->removeElement($superMarkets);
    }

    /**
     * Get superMarkets
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSuperMarkets()
    {
        return $this->superMarkets;
    }
}