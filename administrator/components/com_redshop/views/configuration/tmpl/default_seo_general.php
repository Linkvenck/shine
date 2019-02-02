<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

?>
<legend><?php echo JText::_('COM_REDSHOP_SEO_GENERAL_TAB'); ?></legend>
<div class="form-group">
	<span class="editlinktip hasTip"
		      title="<?php echo JText::_('COM_REDSHOP_ENABLE_SEF_PRODUCT_NUMBER_LBL'); ?>::<?php echo JText::_('COM_REDSHOP_TOOLTIP_ENABLE_SEF_PRODUCT_NUMBER_LBL'); ?>">
		<label
			for="enable_sef_product_number"><?php
			echo JText::_('COM_REDSHOP_ENABLE_SEF_PRODUCT_NUMBER_LBL');
			?></label></span>
	<?php echo $this->lists ['enable_sef_product_number']; ?>
</div>

<div class="form-group">
	<span class="editlinktip hasTip"
		      title="<?php echo JText::_('COM_REDSHOP_TOOLTIP_ENABLE_SEF_NUMBER_NAME_LBL'); ?>::<?php echo JText::_('COM_REDSHOP_TOOLTIP_ENABLE_SEF_NUMBER_NAME_LBL'); ?>">
		<label
			for="enable_sef_product_number"><?php
			echo JText::_('COM_REDSHOP_ENABLE_SEF_NUMBER_NAME_LBL');
			?></label></span>
	<?php echo $this->lists ['enable_sef_number_name']; ?>
</div>

<div class="form-group">
	<span class="editlinktip hasTip"
		      title="<?php echo JText::_('COM_REDSHOP_TOOLTIP_CATEGORY_IN_SEF_URL_LBL'); ?>::<?php echo JText::_('COM_REDSHOP_TOOLTIP_CATEGORY_IN_SEF_URL_LBL'); ?>">
		<label
			for="enable_sef_product_number"><?php
			echo JText::_('COM_REDSHOP_CATEGORY_IN_SEF_URL');
			?></label></span>
	<?php echo $this->lists ['category_in_sef_url']; ?>
</div>

<div class="form-group">
	<span class="editlinktip hasTip"
		      title="<?php echo JText::_('COM_REDSHOP_TOOLTIP_CATEGORY_TREE_IN_SEF_URL_HEAD'); ?>::<?php echo JText::_('COM_REDSHOP_TOOLTIP_CATEGORY_TREE_IN_SEF_URL_LBL'); ?>">
		<label
			for="enable_sef_product_number"><?php
			echo JText::_('COM_REDSHOP_CATEGORY_TREE_IN_SEF_URL');
			?></label></span>
	<?php echo $this->lists ['category_tree_in_sef_url']; ?>
</div>

<div class="form-group">
	<span class="editlinktip hasTip"
		      title="<?php echo JText::_('COM_REDSHOP_AUTOGENERATED_SEO_LBL'); ?>::<?php echo JText::_('COM_REDSHOP_TOOLTIP_AUTOGENERATED_SEO_LBL'); ?>">
		<label
			for="autogenerated_seo">
			<?php
			echo JText::_('COM_REDSHOP_AUTOGENERATED_SEO_LBL');
			?>
		</label></span>
	<?php echo $this->lists ['autogenerated_seo']; ?>
</div>

<div class="form-group">
	<span class="editlinktip hasTip"
		      title="<?php echo JText::_('COM_REDSHOP_SEO_PAGE_LANGAUGE_LBL'); ?>::<?php echo JText::_('COM_REDSHOP_TOOLTIP_SEO_PAGE_LANGAUGE'); ?>">
		<label
			for="seo_page_language">
			<?php
			echo JText::_('COM_REDSHOP_SEO_PAGE_LANGAUGE_LBL');
			?></label></span>
	<textarea class="text_area" type="text" name="seo_page_language"
		              id="seo_page_language" rows="4" cols="40"/><?php
			echo stripslashes($this->config->get('SEO_PAGE_LANGAUGE'));
			?></textarea>
</div>