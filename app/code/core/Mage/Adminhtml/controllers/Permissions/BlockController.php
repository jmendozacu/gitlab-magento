<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition End User License Agreement
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magento.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license http://www.magento.com/license/enterprise-edition
 */

/**
 * Class Mage_Adminhtml_Permissions_BlockController
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Permissions_BlockController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/acl')
            ->_addBreadcrumb($this->__('System'), $this->__('System'))
            ->_addBreadcrumb($this->__('Permissions'), $this->__('Permissions'))
            ->_addBreadcrumb($this->__('Blocks'), $this->__('Blocks'));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
            ->_title($this->__('Permissions'))
            ->_title($this->__('Blocks'));

        /** @var Mage_Adminhtml_Block_Permissions_Block $block */
        $block = $this->getLayout()->createBlock('adminhtml/permissions_block');
        $this->_initAction()
            ->_addContent($block)
            ->renderLayout();
    }

    /**
     * New action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit action
     */
    public function editAction()
    {
        $this->_title($this->__('System'))
            ->_title($this->__('Permissions'))
            ->_title($this->__('Blocks'));

        $id = (int) $this->getRequest()->getParam('block_id');
        $model = Mage::getModel('admin/block');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getBlockName() : $this->__('New Block'));

        // Restore previously entered form data from session
        $data = Mage::getSingleton('adminhtml/session')->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('permissions_block', $model);

        if (isset($id)) {
            $breadcrumb = $this->__('Edit Block');
        } else {
            $breadcrumb = $this->__('New Block');
        }
        $this->_initAction()
            ->_addBreadcrumb($breadcrumb, $breadcrumb);

        $this->getLayout()->getBlock('adminhtml.permissions.block.edit')
            ->setData('action', $this->getUrl('*/permissions_block/save'));

        $this->renderLayout();
    }

    /**
     * Save action
     *
     * @return $this|void
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = (int) $this->getRequest()->getParam('block_id');
            $model = Mage::getModel('admin/block')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            $model->setData($data);
            if ($id) {
                $model->setId($id);
            }
            $result = $model->validate();

            if (is_array($result)) {
                Mage::getSingleton('adminhtml/session')->setUserData($data);
                foreach ($result as $message) {
                    Mage::getSingleton('adminhtml/session')->addError($message);
                }
                $this->_redirect('*/*/edit', array('block_id' => $id));
                return $this;
            }
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The block has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('block_id' => $id));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        $id = (int) $this->getRequest()->getParam('block_id');
        if ($id) {
            try {
                $model = Mage::getModel('admin/block');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Block has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('block_id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find a block to delete.'));
        $this->_redirect('*/*/');
    }

    /**
     * Grid action
     */
    public function blockGridAction()
    {
        $this->getResponse()
            ->setBody($this->getLayout()
                ->createBlock('adminhtml/permissions_block_grid')
                ->toHtml()
            );
    }

    /**
     * Check permissions before allow edit list of blocks
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/acl/blocks');
    }
}
