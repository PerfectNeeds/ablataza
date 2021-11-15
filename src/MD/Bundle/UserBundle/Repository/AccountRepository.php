<?php

namespace MD\Bundle\UserBundle\Repository;

use MD\Utils\SQL;
use MD\Utils\Validate;
use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository {

    public function findAll() {
        return $this->getEntityManager()->getRepository('UserBundle:Account')->findBy(array(), array('id' => 'desc'));
    }

    public function findOneBySlug($slug) {
        $connection = $this->getEntityManager();
        $entity = $connection->getRepository('CMSBundle:Seo')->findOneBySlug('person/' . $slug);
        if ($entity) {
            return $entity->getPerson();
        } else {
            return NULL;
        }
    }

    public function getByRoleId($roleId) {
        $sql = "SELECT * FROM role_account WHERE role_id = ?";
        $filterResult = $this->getEntityManager()->getConnection()
                ->executeQuery($sql, array($roleId));
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('UserBundle:Account')->find($r['account_id'])->getPerson();
        }
        //   exit(var_dump($result));
        return $result;
    }

    // email vervication 
    public function getSelectedAccount($userId, $state) {
        $sql = "SELECT id FROM `account` WHERE MD5(person_id) = ? AND `state`!= ?";
        $filterResult = $this->getEntityManager()->getConnection()
                ->executeQuery($sql, array($userId, $state));
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('UserBundle:Account')->find($r['id']);
        }
        //   exit(var_dump($result));
        if (count($result) > 0)
            return $result[0];
        else
            return null;
    }

    public function getSelectedPerson($userId) {
        $sql = "SELECT id FROM `person` WHERE MD5(id) = ? ";
        $filterResult = $this->getEntityManager()->getConnection()
                ->executeQuery($sql, array($userId));
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('UserBundle:Person')->find($r['id']);
        }
        if (count($result) > 0)
            return $result[0];
        else
            return null;
    }

    public function getFavouriteSuppliers($userId) {
        $sql = "SELECT sa.supplier_id  As sId FROM SupplierFavourite sa    LEFT OUTER JOIN  Supplier s on s.id = sa.supplier_id   AND s.deleted = 0 AND s.hidden = 0   WHERE   sa.Account_id = ? ";
        //exit($sql);
        $filterResult = $this->getEntityManager()->getConnection()
                ->executeQuery($sql, array($userId));
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('ServicesBundle:Supplier')->findOneBy(array("id" => $r['sId'], "hidden" => 0, "deleted" => 0, "state" => 1));
        }
        if (count($result) > 0)
            return $result;
        else
            return null;
    }

    // validate email to be unique 
    public function getUserByEmail($email) {
        $sql = "SELECT id FROM `person` WHERE  `email` = ?";
        $filterResult = $this->getEntityManager()->getConnection()
                ->executeQuery($sql, array($email));
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('UserBundle:Person')->find($r['id']);
        }
        if (count($result) > 0)
            return $result[0];
        else
            return null;
    }

    // validate email to be unique 
    public function getAccountByUSername($email) {
        $sql = "SELECT id FROM `account` WHERE  `username` = ?";
        $filterResult = $this->getEntityManager()->getConnection()
                ->executeQuery($sql, array($email));
        $result = array();
        //exit($sql);
        foreach ($filterResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('UserBundle:Account')->find($r['id']);
        }
        if (count($result) > 0)
            return $result[0];
        else
            return null;
    }

    // validate email to be unique 

    public function getUserByEmailAndId($email, $id) {
        $sql = "SELECT id FROM `account` WHERE  `email` = ? AND id != ? ";
        $filterResult = $this->getEntityManager()->getConnection()
                ->executeQuery($sql, array($email, $id));
        $result = array();
        foreach ($filterResult as $key => $r) {
            $result[$key] = $this->getEntityManager()->getRepository('UserBundle:Account')->find($r['id']);
        }
        if (count($result) > 0)
            return $result[0];
        else
            return null;
    }

}
