<?php
namespace Acme\Service\Common;

abstract class BaseService
{

    protected function createService($name)
    {
        return $this->getKernel()->createService($name);
    }

    protected function createDao($name)
    {
        return $this->getKernel()->createDao($name);
    }

    protected function getKernel()
    {
        return ServiceKernel::instance();
    }

    protected function getContainer()
    {
        return $this->getKernel()->getContainer();
    }

    protected function createServiceException($message = null, $code = 0)
    {
        return new ServiceException($message, $code);
    }

    protected function checkSort($sort, $availableSort) {
        if (empty($sort)) {
            return null;
        }
        if (is_string($sort)) {
            $sort = array($sort);
        }
        if (!is_array($sort) or empty($sort) or (count($sort) > 2)) {
            throw $this->createServiceException('sort error.');
        }
        $sort = array_values($sort);
        if (empty($sort[1])) {
            $sort[1] = 'ASC';
        }
        if (!in_array(strtoupper($sort[1]), array('ASC', 'DESC'))) {
            throw $this->createServiceException('sort error.');
        }
        if (!in_array($sort[0], $availableSort)) {
            throw $this->createServiceException('sort error.');
        }
        return $sort;
    }

}
?>