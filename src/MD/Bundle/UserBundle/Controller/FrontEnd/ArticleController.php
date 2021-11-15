<?php

namespace MD\Bundle\UserBundle\Controller\FrontEnd;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use MD\Utils\Validate as V;
use \MD\Bundle\MediaBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Session\Session;
use MD\Bundle\CMSBundle\Entity\SuperCategory;

/**
 * Article controller.
 * @Route("/article")
 */
class ArticleController extends Controller {

    /**
     * Create a new Account entity.
     * @Route("/profile/{slug}/article/{page}", requirements={"page" = "\d+"}, name="fe_show_profile_article")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Article:viewProfileArticle.html.twig")
     */
    public function viewProfileArticleAction($slug, $page = 1) {
        $em = $this->getDoctrine()->getManager();

        $person = $em->getRepository('UserBundle:Account')->findOneBySlug($slug);

        $search = new \stdClass;
        $search->userId = $person->getId();
        $search->draft = 0;
        $search->publish = 1;

        $articleCount = $em->getRepository('CMSBundle:Article')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($articleCount, $page, 8);
        $articles = $em->getRepository('CMSBundle:Article')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());

        $followers = $em->getRepository('UserBundle:Follower')->getFollowerRandLimit($person->getId(), 4);

        return array(
            'person' => $person,
            'articleCount' => $articleCount,
            'articles' => $articles,
            'paginator' => $paginator->getPagination(),
            'followers' => $followers,
        );
    }

    /**
     * Create a new Account entity.
     * @Route("/my-article/{page}", requirements={"page" = "\d+"}, name="fe_my_articale")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Article:myArticale.html.twig")
     */
    public function myArticaleAction($page = 1) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $search = new \stdClass;
        $search->userId = $this->get('security.context')->getToken()->getUser()->getPerson()->getId();
        $search->draft = 1;
        $search->publish = 1;
        $em = $this->getDoctrine()->getManager();
        $count = $em->getRepository('CMSBundle:Article')->filter($search, TRUE);
        $paginator = new \MD\Bundle\CMSBundle\Lib\Paginator($count, $page, 10);

        $entities = $em->getRepository('CMSBundle:Article')->filter($search, FALSE, $paginator->getLimitStart(), $paginator->getPageLimit());
        return array(
            "entities" => $entities,
            'paginator' => $paginator->getPagination(),
        );
    }

    /**
     * Create a new Account entity.
     * @Route("/add-article", name="fe_add_article")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Article:addArticle.html.twig")
     */
    public function addArticleAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $em = $this->getDoctrine()->getManager();
        $superCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_ARTICLE, 'deleted' => FALSE));
        $form = $this->createForm(new \MD\Bundle\UserBundle\Form\PersonType());
        return array(
            "superCategories" => $superCategories,
            'form' => $form->createView(),
        );
    }

    /**
     * Deletes a Article entity.
     *
     * @Route("/delete-article", name="fe_article_delete")
     * @Method("POST")
     */
    public function deleteArticleAction(Request $request) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $id = $this->getRequest()->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CMSBundle:Article')->find($id);


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            throw $this->createNotFoundException('Access Denied.');
        }

        $entity->setDeleted(TRUE);
        $em->persist($entity);
        $em->flush();
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'تم الحذف بنجاح');
        return $this->redirect($this->generateUrl('fe_my_articale'));
    }

    /**
     * Creates a new Article entity.
     *
     * @Route("/create-article", name="fe_article_create")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Article:addArticle.html.twig")
     */
    public function createArticleAction(Request $request) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $entity = new \MD\Bundle\CMSBundle\Entity\Article();

        $em = $this->getDoctrine()->getManager();
        $post = $this->getRequest()->request->get('post');
        $data = $this->getRequest()->request->get('data');
        $action = $this->getRequest()->request->get('action');

        $return = TRUE;
        $error = array();
        if (!V::not_null($data['title'])) {
            array_push($error, "Title");
            $return = FALSE;
        }
        if (count($data['superCategories']) == 0) {
            array_push($error, "Category");
            $return = FALSE;
        }

        if (!V::not_null($post['description'])) {
            array_push($error, "description");
            $return = FALSE;
        }

        if (count($error) > 0) {
            $return = 'You must enter ';
            for ($i = 0; $i < count($error); $i++) {
                if (count($error) == $i + 1) {
                    $return .= $error[$i];
                } else {
                    if (count($error) == $i + 2) {
                        $return .= $error[$i] . ' and ';
                    } else {
                        $return .= $error[$i] . ', ';
                    }
                }
            }
            $session = new Session();
            $session->getFlashBag()->add('error', $return);

            return $this->redirect($this->generateUrl('fe_add_article'));
        }

        if ($action == "draft") {
            $entity->setDraft(TRUE);
        } else {
            $entity->setDraft(FALSE);
        }

        $entity->setTitle($data['title']);
        $entity->setPublish(False);

        if (isset($data['superCategories'])) {
            foreach ($data['superCategories'] as $superCategory) {
                $superCategoryEntity = $em->getRepository('CMSBundle:SuperCategory')->find($superCategory);
                $entity->addSuperCategorie($superCategoryEntity);
            }
        }

        $postEntity = new \MD\Bundle\CMSBundle\Entity\Post();
        $content = array(
            'description' => $post['description'],
        );
        $postEntity->setContent($content);
        $em->persist($postEntity);
        $em->flush();

        $person = $em->getRepository('UserBundle:Person')->find($this->getUser()->getPerson()->getId());
        $entity->setPerson($person);
        $em->persist($entity);
        $em->flush();

        $seoEntity = new \MD\Bundle\CMSBundle\Entity\Seo();
        $seoEntity->setSlug('article/' . $entity->getId() . '-' . $entity->getTitle());
        $em->persist($seoEntity);
        $em->flush();

        $entity->setSeo($seoEntity);
        $entity->setPost($postEntity);
        $em->persist($entity);
        $em->flush();
        $em->refresh($entity);

//        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType());
//        $formView = $uploadForm->createView();
//        $uploadForm->bind($request);
//        $data_upload = $uploadForm->getData();
//        $file = $data_upload["file"];
//
//        $imageController = new \MD\Bundle\MediaBundle\Controller\ImageController();
//        $imageController->uploadSingleImage($em, $entity, $file, 5);

        $uploadForm = $this->createForm(new \MD\Bundle\MediaBundle\Form\SingleImageType('files'));
        $uploadForm->bind($request);
        $data_upload = $uploadForm->getData();
        $files = $data_upload["files"];
        foreach ($files as $file) {

            if ($file != null) {
                $uploadPath = 'article/image/';
                $imageId = $entity->getPost()->getId();
                $mainImage = $entity->getPost()->getMainImage();
                $image = new Image();

                if ($mainImage) {
                    $image->setImageType(Image::TYPE_GALLERY);
                } else {
                    $image->setImageType(Image::TYPE_MAIN);
                }
                $em->persist($image);
                $em->flush();
                $image->setFile($file);
                $image->preUpload();
                $image->upload($uploadPath . $imageId);
                $entity->getPost()->addImage($image);
                $em->persist($entity);
                $em->flush();
            }
        }

        $session = new Session();
        $this->getRequest()->getSession()->getFlashBag()->add('success', 'شكرا مقالك وصل لأبلة طازة وهتنزله لحبايبها بعد مراجعته شاركينا أكتر بمقالاتك ');

        return $this->redirect($this->generateUrl('fe_my_articale'));
    }

    /**
     * Displays a form to edit an existing Article entity.
     *
     * @Route("/{slug}/edit-article", name="fe_article_edit")
     * @Method("GET")
     * @Template("UserBundle:FrontEnd/Article:editArticle.html.twig")
     */
    public function editArticleAction($slug) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Article')->findOneBySlug($slug);

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            $this->createNotFoundException('Access Denied.');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $superCategories = $em->getRepository('CMSBundle:SuperCategory')->findBy(array('type' => SuperCategory::TYPE_ARTICLE, 'deleted' => FALSE));

        $form = $this->createForm(new \MD\Bundle\UserBundle\Form\PersonType());
        return array(
            'entity' => $entity,
            'superCategories' => $superCategories,
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing Article entity.
     *
     * @Route("/update-article/{slug}", name="fe_article_update")
     * @Method("POST")
     * @Template("UserBundle:FrontEnd/Article:editArticle.html.twig")
     */
    public function updateArticleAction($slug, Request $request) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CMSBundle:Article')->findOneBySlug($slug);

        if ($entity->getPerson()->getId() != $this->getUser()->getPerson()->getId()) {
            $this->createNotFoundException('Access Denied.');
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article entity.');
        }

        $post = $this->getRequest()->request->get('post');
        $data = $this->getRequest()->request->get('data');


        $return = TRUE;
        $error = array();
        if (!V::not_null($data['title'])) {
            array_push($error, "Title");
            $return = FALSE;
        }
        if (count($data['superCategories']) == 0) {
            array_push($error, "Category");
            $return = FALSE;
        }

        if (!V::not_null($post['description'])) {
            array_push($error, "description");
            $return = FALSE;
        }

        if (count($error) > 0) {
            $return = 'You must enter ';
            for ($i = 0; $i < count($error); $i++) {
                if (count($error) == $i + 1) {
                    $return .= $error[$i];
                } else {
                    if (count($error) == $i + 2) {
                        $return .= $error[$i] . ' and ';
                    } else {
                        $return .= $error[$i] . ', ';
                    }
                }
            }
            $session = new Session();
            $session->getFlashBag()->add('error', $return);

            return $this->redirect($this->generateUrl('fe_article_edit', array('slug' => $entity->getSeo()->getSlug())));
        }

        if (isset($data['draft']) AND $data['draft'] == "1") {
            $entity->setDraft(TRUE);
        } else {
            $entity->setDraft(FALSE);
        }

        $entity->setTitle($data['title']);
        $entity->setPublish(False);

        $em->getRepository('CMSBundle:SuperCategory')->removeSuperCategoriesByArticleId($entity->getId());
        if (isset($data['superCategories'])) {
            foreach ($data['superCategories'] as $superCategory) {
                $superCategoryEntity = $em->getRepository('CMSBundle:SuperCategory')->find($superCategory);
                $entity->addSuperCategorie($superCategoryEntity);
            }
        }

        $postEntity = $entity->getPost();
        $content = array(
            'description' => $post['description'],
        );
        $postEntity->setContent($content);
        $em->persist($postEntity);
        $em->flush();


        $em->persist($entity);
        $em->flush();

        $this->getRequest()->getSession()->getFlashBag()->add('success', 'تم التعديل بنجاح');

        return $this->redirect($this->generateUrl('fe_my_articale'));
    }

    /**
     * Set Images to Property.
     *
     * @Route("/gallery-image-article/{id}/" , name="fe_article_edit_create_images")
     * @Method("POST")
     */
    public function setArticleImageAction(Request $request, $id) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }

        $form = $this->createForm(new \MD\Bundle\MediaBundle\Form\ImageType());
        $formView = $form->createView();
        $form->bind($request);

        $data = $form->getData();
        $files = $data["files"];

        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('CMSBundle:Article')->find($id);
        $entity = $article->getPost();
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Landmark entity.');
        }

        $uploadPath = 'article/';

        $imageType = $request->get("type");
        foreach ($files as $file) {
            if ($file != NULL) {
                $image = new \MD\Bundle\MediaBundle\Entity\Image();
                $em->persist($image);
                $em->flush();
                $image->setFile($file);
                $mainImages = $entity->getImages(array(\MD\Bundle\MediaBundle\Entity\Image::TYPE_MAIN));
                if ($imageType == Image::TYPE_MAIN && count($mainImages) > 0) {
                    foreach ($mainImages As $mainImage) {
                        $entity->removeImage($mainImage);
                        $em->persist($entity);
                        $em->flush();
                        $image->storeFilenameForRemove($uploadPath . 'image/' . $entity->getId());
                        $image->removeUpload();
//                        $image->storeFilenameForResizeRemove("suppliers/" . $entity->getId());
//                        $image->removeResizeUpload();
                        $em->persist($mainImage);
                        $em->flush();
                        $em->remove($mainImage);
                        $em->flush();
                        $image->setImageType(Image::TYPE_MAIN);
                    }
                } else if ($imageType == \MD\Bundle\MediaBundle\Entity\Image::TYPE_MAIN && count($mainImages) == 0) {
                    $image->setImageType(Image::TYPE_MAIN);
                } else {
                    $image->setImageType(Image::TYPE_GALLERY);
                }


                $image->preUpload();
                $image->upload($uploadPath . 'image/' . $entity->getId());
                $entity->addImage($image);
                $imageUrl = $this->container->get('templating.helper.assets')->getUrl("uploads/" . $uploadPath . 'image/' . $entity->getId() . "/" . $image->getId());
                $imageId = $image->getId();
            }
            $em->persist($entity);
            $em->flush();
            $files = '{"files":[{"url":"' . $imageUrl . '","thumbnailUrl":"http://lh6.ggpht.com/0GmazPJ8DqFO09TGp-OVK_LUKtQh0BQnTFXNdqN-5bCeVSULfEkCAifm6p9V_FXyYHgmQvkJoeONZmuxkTBqZANbc94xp-Av=s80","name":"test","id":"' . $imageId . '","type":"image/jpeg","size":620888,"deleteUrl":"http://localhost/packagat/web/uploads/packages/1/th71?delete=true","deleteType":"DELETE"}]}';
            $response = new Response();
            $response->setContent($files);
            $response->setStatusCode(200);
            return $response;
        }

        return array(
            'form' => $formView,
            'id' => $id,
        );
    }

    /**
     * Displays a form to create a new PropertyGallery entity.
     *
     * @Route("/gallery/ajax/", name = "fe_article_edit_main_image_ajax")
     * @Method("POST")
     */
    public function SetimageMainAction() {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }
        $id = $this->getRequest()->request->get('id');
        $image_id = $this->getRequest()->request->get('image_id');
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('MediaBundle:Image')->setMainImage('CMSBundle:Post', $id, $image_id);
    }

    /**
     * Deletes a PropertyGallery entity.
     *
     * @Route("/delete-article-image/{id}", name="fe_article_edit_images_delete")
     * @Method("POST")
     */
    public function deleteArticleImageAction($id) {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('login'));
        }

        $image_id = $this->getRequest()->request->get('id');
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('CMSBundle:Article')->find($id);
        $entity = $em->getRepository('CMSBundle:Post')->find($article->getPost()->getId());
        if (!$entity) {
            throw $this->createNotFoundException('Unable to Post Team entity.');
        }
        $image = $em->getRepository('MediaBundle:Image')->find($image_id);
        if (!$image) {
            throw $this->createNotFoundException('Unable to find Team entity.');
        }
        $entity->removeImage($image);
        $em->persist($entity);
        $em->flush();

        $uploadPath = 'article/';

        $image->storeFilenameForRemove($uploadPath . 'image/' . $entity->getId());
        $image->removeUpload();
        $em->remove($image);
        $em->flush();

        return $this->redirect($this->generateUrl('fe_article_edit', array('slug' => $article->getSeo()->getSlug())));
    }

}
