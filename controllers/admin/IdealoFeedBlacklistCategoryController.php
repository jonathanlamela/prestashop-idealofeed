<?php

require_once _PS_MODULE_DIR_ . "idealofeed/classes/IdealoBlacklistCategoryItem.php";

class IdealoFeedBlacklistCategoryController extends AdminControllerCore
{
    public function __construct()
    {

        $this->bootstrap = true;
        $this->table = 'idealofeed_blacklist_categories';
        $this->className = 'IdealoBlacklistCategoryItem';
        $this->lang = false;
        $this->deleted = false;
        $this->explicitSelect = true;
        $this->allow_export = true;

        $this->context = Context::getContext();

        $this->addRowAction('edit');
        $this->addRowAction('delete');



        $this->fields_list = array(
            'id_idealofeed_blacklist_categories' => array(
                'title' => Context::getContext()->getTranslator()->trans('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'category_id' => array(
                'title' => Context::getContext()->getTranslator()->trans('ID Categoria'),
                'align' => 'left',
                'orderby' => false
            ),

        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => Context::getContext()->getTranslator()->trans('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => Context::getContext()->getTranslator()->trans('Delete selected items?')
            )
        );
        $this->specificConfirmDelete = false;

        parent::__construct();
    }

    public function initContent()
    {
        if ($this->action == 'select_delete') {
            $this->context->smarty->assign(array(
                'delete_form' => true,
                'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
                'boxes' => $this->boxes,
            ));
        }
        parent::initContent();
    }

    public function init()
    {
        parent::init();
    }

    public function renderForm()
    {
        $this->display = 'edit';
        $this->initToolbar();

        $obj = $this->loadObject(true);

        $this->fields_form = array(
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => "ID Categoria",
                    'name' => 'category_id',
                    'id' => 'title',
                    'required' => "true",
                    'size' => 50
                ),

            ),
            'submit' => array(
                'title' => Context::getContext()->getTranslator()->trans('Save')
            )
        );


        $this->fields_value = array(
            'category_id' => $obj->category_id ? $obj->category_id : "",

        );


        return parent::renderForm();
    }
}
