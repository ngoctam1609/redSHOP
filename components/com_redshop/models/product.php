<?php
/**
 * @package     redSHOP
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2012 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die ('Restricted access');

require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'product.php');
require_once (JPATH_COMPONENT . DS . 'helpers' . DS . 'extra_field.php');
require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_redshop' . DS . 'helpers' . DS . 'shipping.php');
require_once (JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_redshop' . DS . 'helpers' . DS . 'extra_field.php');
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'core' . DS . 'model.php';

class productModelproduct extends RedshopCoreModel
{
    public $_product = null;

    public $_template = null;

    public $_catid = null;

    public function __construct()
    {
        parent::__construct();

        $pid = JRequest::getInt('pid', 0);

        $GLOBALS['childproductlist'] = array();

        $this->_id    = $pid;
        $this->_catid = ( int )JRequest::getVar('cid', 0);
    }

    public function _buildQuery()
    {
        $and = "";

        // Shopper group - choose from manufactures Start
        $rsUserhelper               = new rsUserhelper();
        $shopper_group_manufactures = $rsUserhelper->getShopperGroupManufacturers();

        if ($shopper_group_manufactures != "")
        {
            $and .= " AND p.manufacturer_id IN (" . $shopper_group_manufactures . ") ";
        }

        // Shopper group - choose from manufactures End
        if (isset ($this->_catid) && $this->_catid != 0)
        {
            $and .= "AND pcx.category_id='" . $this->_catid . "' ";
        }
        $query = "SELECT p.*, c.category_id, c.category_name ,c.category_back_full_image,c.category_full_image , m.manufacturer_name,pcx.ordering " . "FROM " . $this->_table_prefix . "product AS p " . "LEFT JOIN " . $this->_table_prefix . "product_category_xref AS pcx ON pcx.product_id = p.product_id " . "LEFT JOIN " . $this->_table_prefix . "manufacturer AS m ON m.manufacturer_id = p.manufacturer_id " . "LEFT JOIN " . $this->_table_prefix . "category AS c ON c.category_id = pcx.category_id " . "WHERE 1=1 " . "AND p.product_id ='" . $this->_id . "' " . $and . "LIMIT 0,1 ";
        return $query;
    }

    public function getData()
    {
        $redTemplate = new Redtemplate ();
        if (empty ($this->_data))
        {
            $query = $this->_buildQuery();
            $this->_db->setQuery($query);
            $this->_data = $this->_db->loadObject();
        }
        $this->_data->product_s_desc = $redTemplate->parseredSHOPplugin($this->_data->product_s_desc);
        $this->_data->product_desc   = $redTemplate->parseredSHOPplugin($this->_data->product_desc);

        return $this->_data;
    }

    public function getProductTemplate()
    {
        $redTemplate = new Redtemplate ();
        if (empty ($this->_template))
        {
            $this->_template = $redTemplate->getTemplate("product", $this->_data->product_template);
            $this->_template = $this->_template[0];
        }
        return $this->_template;
    }

    /**
     * get next or previous product using ordering
     *
     * @params: $product_id - current product id
     * @params: $category_id - current product category id
     * @params: $dirn - direction to indicate next or previous product
     *
     * @return: object array
     */
    public function getPrevNextproduct($product_id, $category_id, $dirn)
    {
        $query = "SELECT ordering FROM " . $this->_table_prefix . "product_category_xref WHERE product_id = " . (int)$product_id . " AND category_id = " . (int)$category_id . " LIMIT 0,1";

        $where = ' AND p.published="1" AND category_id = ' . (int)$category_id;

        $sql = "SELECT pcx.product_id, p.product_name , ordering FROM " . $this->_table_prefix . "product_category_xref ";

        $sql .= " as pcx LEFT JOIN " . $this->_table_prefix . "product as p ON p.product_id = pcx.product_id ";

        if ($dirn < 0)
        {
            $sql .= ' WHERE ordering < (' . $query . ')';
            $sql .= $where;
            $sql .= ' ORDER BY ordering DESC';
        }
        else if ($dirn > 0)
        {
            $sql .= ' WHERE ordering > (' . $query . ')';
            $sql .= $where;
            $sql .= ' ORDER BY ordering';
        }
        else
        {
            $sql .= ' WHERE ordering = (' . $query . ')';
            $sql .= $where;
            $sql .= ' ORDER BY ordering';
        }
        $this->_db->setQuery($sql, 0, 1);
        $row = null;
        $row = $this->_db->loadObject();
        return $row;
    }

    // Product Tags Functions
    public function getProductTags($tagname, $productid)
    {
        $query = "SELECT pt.*,ptx.product_id,ptx.users_id " . "FROM " . $this->_table_prefix . "product_tags AS pt " . "LEFT JOIN " . $this->_table_prefix . "product_tags_xref AS ptx ON pt.tags_id=ptx.tags_id " . "WHERE pt.tags_name LIKE '" . $tagname . "' ";
        $this->_db->setQuery($query);
        $list = $this->_db->loadObjectlist();
        return $list;
    }

    public function updateVisited($product_id)
    {
        $query = "UPDATE " . $this->_table_prefix . "product " . "SET visited=visited + 1 " . "WHERE product_id='" . $product_id . "' ";
        $this->_db->setQuery($query);
        $this->_db->Query();
    }

    public function addProductTags($data)
    {
        $tags = $this->getTable('product_tags');
        if (!$tags->bind($data))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$tags->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return $tags;
    }

    public function addtowishlist($data)
    {
        $row = $this->getTable('wishlist');
        if (!$row->bind($data))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        if (!$row->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        return $row;
    }

    public function addtowishlist2session($data)
    {
        ob_clean();
        $extraField = new extraField();
        $section    = 12;
        $row_data   = $extraField->getSectionFieldList($section);

        for ($check_i = 1; $check_i <= $_SESSION ["no_of_prod"]; $check_i++)
        {
            if ($_SESSION ['wish_' . $check_i]->product_id == $data ['product_id'])
            {
                if ($data['task'] != "")
                {

                    unset($_SESSION["no_of_prod"]);
                }
            }
        }

        $_SESSION ["no_of_prod"] += 1;
        $no_prod_i = 'wish_' . $_SESSION ["no_of_prod"];

        $_SESSION [$no_prod_i]->product_id = $data ['product_id'];
        $_SESSION [$no_prod_i]->comment    = isset ($data ['comment']) ? $data ['comment'] : "";
        $_SESSION [$no_prod_i]->cdate      = $data ['cdate'];
        for ($k = 0; $k < count($row_data); $k++)
        {

            $myfield                        = "productuserfield_" . $k;
            $_SESSION[$no_prod_i]->$myfield = $data['productuserfield_' . $k];
        }

        return true;
    }

    public function addProductTagsXref($post, $tags)
    {
        $user  = JFactory::getUser();
        $query = "INSERT INTO " . $this->_table_prefix . "product_tags_xref " . "VALUES('" . $tags->tags_id . "','" . $post['product_id'] . "','" . $user->id . "')";
        $this->_db->setQuery($query);
        $this->_db->Query();
        return true;
    }

    public function checkProductTags($tagname, $productid)
    {
        $user  = JFactory::getUser();
        $query = "SELECT pt.*,ptx.product_id,ptx.users_id FROM " . $this->_table_prefix . "product_tags AS pt " . "LEFT JOIN " . $this->_table_prefix . "product_tags_xref AS ptx ON pt.tags_id=ptx.tags_id " . "WHERE pt.tags_name LIKE '" . $tagname . "' " . "AND ptx.product_id='" . $productid . "' " . "AND ptx.users_id='" . $user->id . "' ";
        $this->_db->setQuery($query);
        $list = $this->_db->loadObject();
        return $list;
    }

    public function checkWishlist($product_id)
    {
        $user  = JFactory::getUser();
        $query = "SELECT * FROM " . $this->_table_prefix . "wishlist " . "WHERE product_id='" . $product_id . "' " . "AND user_id='" . $user->id . "' ";
        $this->_db->setQuery($query);
        $list = $this->_db->loadObject();
        return $list;
    }

    public function checkComparelist($product_id)
    {
        $session         = JFactory::getSession();
        $compare_product = $session->get('compare_product');
        $cid             = JRequest::getInt('cid');
        $catid           = $compare_product[0]['category_id'];
        if (PRODUCT_COMPARISON_TYPE == 'category' && $catid != $cid)
        {
            unset($compare_product);
            $compare['idx'] = 0;
        }

        if ($product_id != 0)
        {
            if (!$compare_product)
            {
                return true; // return true to store product in compare product cart.
            }
            else
            {
                $idx = (int)($compare_product['idx']);

                for ($i = 0; $i < $idx; $i++)
                {
                    if ($compare_product[$i]["product_id"] == $product_id)
                    {
                        return false; // return false if product is already in compare product cart
                    }
                }
                return true;
            }
        }
        /* if public function is called for total product in cart than return no of product in cart*/
        return isset($compare_product['idx']) ? (int)($compare_product['idx']) : 0;
    }

    public function addtocompare($data)
    {
        $session         = JFactory::getSession();
        $compare_product = $session->get('compare_product');
        if (!$compare_product)
        {
            $compare_product        = array();
            $compare_product['idx'] = 0;

            $session->set('compare_product', $compare_product);
            $compare_product = $session->get('compare_product');
        }
        $idx = (int)($compare_product['idx']);
        if (PRODUCT_COMPARISON_TYPE == 'category' && $compare_product[0]["category_id"] != $data["cid"])
        {
            unset($compare_product);
            $idx = 0;
        }
        $compare_product[$idx]["product_id"]  = $data["pid"];
        $compare_product[$idx]["category_id"] = $data["cid"];

        $compare_product['idx'] = $idx + 1;
        $session->set('compare_product', $compare_product);
        return true;
    }

    public function removeCompare($product_id)
    {
        $session         = JFactory::getSession();
        $compare_product = $session->get('compare_product');

        if (!$compare_product)
        {
            return;
        }
        $tmp_array = array();
        $idx       = (int)($compare_product['idx']);
        $tmp_i     = 0;
        for ($i = 0; $i < $idx; $i++)
        {
            if ($compare_product[$i]["product_id"] != $product_id)
            {
                $tmp_array[] = $compare_product[$i];
            }
            else
            {
                $tmp_i++;
            }
        }
        $idx -= $tmp_i;
        if ($idx < 0)
        {
            $idx = 0;
        }
        $compare_product        = $tmp_array;
        $compare_product['idx'] = $idx;
        $session->set('compare_product', $compare_product);
        return true;
    }

    public function downloadProduct($tid)
    {
        $query = "SELECT * FROM " . $this->_table_prefix . "product_download AS pd " . "LEFT JOIN " . $this->_table_prefix . "media AS m ON m.media_name = pd.file_name " . "WHERE download_id='" . $tid . "' ";
        $this->_db->setQuery($query);
        $list = $this->_db->loadObject();
        return $list;
    }

    public function AdditionaldownloadProduct($mid = 0, $id = 0, $media = 0)
    {
        $where = "";
        if ($mid != 0)
        {
            $where .= "AND media_id='" . $mid . "' ";
        }
        if ($id != 0)
        {
            $where .= "AND id='" . $id . "' ";
        }
        if ($media != 0)
        {
            $tablename = "media ";
        }
        else
        {
            $tablename = "media_download ";
        }
        $query = "SELECT * FROM " . $this->_table_prefix . $tablename . "WHERE 1=1 " . $where;
        $list  = $this->_getList($query);
        return $list;
    }

    public function setDownloadLimit($did)
    {
        $query = "UPDATE " . $this->_table_prefix . "product_download " . "SET download_max=(download_max - 1) " . "WHERE download_id='" . $did . "' ";
        $this->_db->setQuery($query);
        $ret = $this->_db->Query();
        if ($ret)
        {
            return true;
        }
        return false;
    }

    public function getAllChildProductArrayList($childid = 0, $parentid = 0)
    {
        $producthelper = new producthelper ();
        $info          = $producthelper->getChildProduct($parentid);

        for ($i = 0; $i < count($info); $i++)
        {
            if ($childid != $info[$i]->product_id)
            {
                $GLOBALS['childproductlist'][] = $info[$i];
                $this->getAllChildProductArrayList($childid, $info[$i]->product_id);
            }
        }
        return $GLOBALS['childproductlist'];
    }
}

