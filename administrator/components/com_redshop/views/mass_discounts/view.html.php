<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * The mass discounts view
 *
 * @package     RedSHOP.Backend
 * @subpackage  States.View
 * @since       2.0.3
 */
class RedshopViewMass_Discounts extends RedshopViewAdmin
{
	/**
	 * @var  array
	 */
	public $items;

	/**
	 * @var  JPagination
	 */
	public $pagination;

	/**
	 * @var  array
	 */
	public $state;

	/**
	 * @var  array
	 */
	public $activeFilters;

	/**
	 * @var  JForm
	 */
	public $filterForm;

	/**
	 * Display the States view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @throws  Exception
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get data from the model
		$model = $this->getModel();

		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->activeFilters = $model->getActiveFilters();
		$this->filterForm    = $model->getForm();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode('<br />', $errors));

			return false;
		}

		// Set the tool-bar and number of found items
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   2.0.3
	 */
	protected function addToolBar()
	{
		$title = JText::_('COM_REDSHOP_MASS_DISCOUNT_MANAGEMENT');

		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'>(" . $this->pagination->total . ")</span>";
		}

		JToolBarHelper::title($title);
		JToolBarHelper::addNew('mass_discount.add');
		JToolBarHelper::editList('mass_discount.edit');
		JToolBarHelper::deleteList('', 'mass_discounts.delete');
		JToolbarHelper::publish('mass_discounts.publish', 'JTOOLBAR_PUBLISH', true);
		JToolbarHelper::unpublish('mass_discounts.unpublish', 'JTOOLBAR_UNPUBLISH', true);
	}
}
