<?php
/**
 * @package     RedSHOP.Backend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

JHTML::_('behavior.tooltip');
JHtml::_('behavior.modal', 'a.joom-box');


$producthelper = productHelper::getInstance();
$carthelper = rsCarthelper::getInstance();
$order_functions = order_functions::getInstance();
$redhelper = redhelper::getInstance();
$extra_field = extra_field::getInstance();
$shippinghelper = shipping::getInstance();
$config = Redconfiguration::getInstance();

$uri = JURI::getInstance();
$url = $uri->root();


$tmpl = JRequest::getVar('tmpl');
$model = $this->getModel('order_detail');
$session = JFactory::getSession();
$billing = $this->billing;
$shipping = $this->shipping;
$is_company = $billing->is_company;
$order_id = $this->detail->order_id;
$products = $order_functions->getOrderItemDetail($order_id);
$log_rec = $model->getOrderLog($order_id);

if (!$shipping)
{
	$shipping = $billing;
}
$session->set('shipp_users_info_id', $shipping->users_info_id);

# get Downloadable Products
$downloadProducts = $order_functions->getDownloadProduct($order_id);
$totalDownloadProduct = count($downloadProducts);
$dproducts = array();
for ($t = 0; $t < $totalDownloadProduct; $t++)
{
	$downloadProduct = $downloadProducts[$t];
	$dproducts[$downloadProduct->product_id][$downloadProduct->download_id] = $downloadProduct;
}
?>
<script type="text/javascript">
	var rowCount = 1;

	function submitbutton(pressbutton, form) {
		if (pressbutton == 'add') {
			if (form.product1.value == 0) {
				alert("<?php echo JText::_('SELECT_PRODUCT');?>");
				return false;
			}
			else if (validateExtrafield(form) == false) {
				return false;
			}
			else {
				form.task.value = 'neworderitem';
				form.submit();
				return true;
			}
		}
	}
</script>

<div class="row">
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-blue">
				<i class="fa fa-calendar" aria-hidden="true"></i>
			</span>

			<div class="info-box-content">
				<span class="info-box-text"><?php echo JText::_('COM_REDSHOP_ORDER_DATE');?></span>
				<span class="info-box-number"><?php echo $config->convertDateFormat($this->detail->cdate); ?></span>
			</div>
		</div>
	</div>
	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-green">
				<i class="fa fa-money" aria-hidden="true"></i>
			</span>

			<div class="info-box-content">
				<span class="info-box-text"><?php echo JText::_('COM_REDSHOP_ORDER_TOTAL');?></span>
				<span class="info-box-number"><?php echo $producthelper->getProductFormattedPrice($this->detail->order_total);?></span>
			</div>
		</div>
	</div>

	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-aqua">
				<i class="fa fa-shopping-cart" aria-hidden="true"></i>
			</span>

			<div class="info-box-content">
				<span class="info-box-text"><?php echo JText::_('COM_REDSHOP_PRODUCTS');?></span>
				<span class="info-box-number"><?php echo count($products) ?></span>
			</div>
		</div>
	</div>

	<div class="col-md-3 col-sm-6 col-xs-12">
		<div class="info-box">
			<span class="info-box-icon bg-yellow">
				<i class="fa fa-area-chart" aria-hidden="true"></i>
			</span>

			<div class="info-box-content">
				<span class="info-box-text"><?php echo JText::_('COM_REDSHOP_ORDER_STATUS');?></span>
				<span class="info-box-number"><?php echo $this->detail->order_payment_status ?></span>
			</div>
		</div>
	</div>
</div>

<div class="tab-content">
	<div class="row">
		<div class="col-sm-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo JText::_('COM_REDSHOP_ORDER_INFORMATION'); ?></h3>
				</div>
				<div class="box-body">
					<form action="index.php?option=com_redshop" method="post" name="adminForm" id="adminForm">
						<table border="0" cellspacing="0" cellpadding="0" class="adminlist table table-striped table-condensed">
							<tbody>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_ORDER_ID'); ?>:</td>
								<td><?php echo $order_id; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_ORDER_NUMBER'); ?>:</td>
								<td><?php echo $this->detail->order_number; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_INVOICE_NUMBER'); ?>:</td>
								<td><?php echo $this->detail->invoice_number; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_ORDER_DATE'); ?>:</td>
								<td><?php echo $config->convertDateFormat($this->detail->cdate); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_ORDER_PAYMENT_METHOD'); ?>:</td>
								<td><?php echo JText::_($this->payment_detail->order_payment_name); ?>
									<?php if (count($model->getccdetail($order_id)) > 0)
									{ ?>
										<a href="<?php echo JRoute::_('index.php?option=com_redshop&view=order_detail&task=ccdetail&cid[]=' . $order_id); ?>"
										   class="joom-box btn btn-primary"
										   rel="{handler: 'iframe', size: {x: 550, y: 200}}"><?php echo JText::_('COM_REDSHOP_CLICK_TO_VIEW_CREDIT_CARD_DETAIL');?></a>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_ORDER_PAYMENT_EXTRA_FILEDS'); ?>:</td>
								<td><?php echo $PaymentExtrafields = $producthelper->getPaymentandShippingExtrafields($this->detail, 18); ?>

								</td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_ORDER_PAYMENT_REFERENCE_NUMBER'); ?>:</td>
								<td><?php
									if ($this->payment_detail->order_payment_trans_id != "")
									{
										echo $this->payment_detail->order_payment_trans_id;
									}
									else
									{
										echo "N/A";
									}
									?>
								</td>
							</tr>
							<?php //if($is_company){?>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_REQUISITION_NUMBER'); ?>:</td>
								<td><input class="inputbox" name="requisition_number" id="requisition_number"
										   type="text"
										   value="<?php echo $this->detail->requisition_number; ?>"/></td>
							</tr>
							<?php //}?>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_ORDER_STATUS'); ?>:</td>
								<td>
									<?php

									$send_mail_to_customer = 0;
									if (Redshop::getConfig()->get('SEND_MAIL_TO_CUSTOMER'))
									{
										$send_mail_to_customer = "checked";
									}

									$linkupdate = JRoute::_('index.php?option=com_redshop&view=order&task=update_status&return=order_detail&order_id[]=' . $order_id);

									echo $order_functions->getstatuslist('status', $this->detail->order_status, "class=\"inputbox\" size=\"1\" ");
									echo "&nbsp";
									echo $order_functions->getpaymentstatuslist('order_paymentstatus', $this->detail->order_payment_status, "class=\"inputbox\" size=\"1\" ");
									?>
									<?php if ($tmpl)
									{ ?>
										<input type="hidden" name="tmpl" value="<?php echo $tmpl ?>">
									<?php } ?>
									<label class="checkbox inline">
									<input type="checkbox" <?php echo $send_mail_to_customer;?>  value="true"
										   name="order_sendordermail"
										   id="order_sendordermail"/><?php echo JText::_('COM_REDSHOP_SEND_ORDER_MAIL'); ?>
									</label>
									<input class="button btn btn-primary" onclick="this.form.submit();" name="order_status"
										   value="<?php echo JText::_('COM_REDSHOP_UPDATE_STATUS_BUTTON'); ?>" type="button">
									<br/><br/>
									<?php
									$partial_paid = $order_functions->getOrderPartialPayment($order_id);


									?>
								</td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_COMMENT'); ?>:</td>
								<td>
									<textarea cols="50" rows="5"
											  name="customer_note"><?php echo $this->detail->customer_note;?></textarea>
								</td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_CUSTOMER_IP_ADDRESS'); ?>:</td>
								<td><?php echo $this->detail->ip_address; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_CUSTOMER_MESSAGE_LBL'); ?>:</td>
								<td><?php echo $this->detail->customer_message; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_REDSHOP_REFERRAL_CODE_LBL'); ?>:</td>
								<td><?php echo $this->detail->referral_code; ?></td>
							</tr>
							<tr>
								<td align="left"><?php echo JText::_('COM_REDSHOP_DISCOUNT_TYPE_LBL'); ?>:</td>
								<td>
								<?php
									$arr_discount_type = array();
									$arr_discount = explode('@', $this->detail->discount_type);
									$discount_type = '';
									for ($d = 0, $dn = count($arr_discount); $d < $dn; $d++)
									{
										if ($arr_discount[$d])
										{
											$arr_discount_type = explode(':', $arr_discount[$d]);

											if ($arr_discount_type[0] == 'c')
												$discount_type .= JText::_('COM_REDSHOP_COUPON_CODE') . ' : ' . $arr_discount_type[1] . '<br>';
											if ($arr_discount_type[0] == 'v')
												$discount_type .= JText::_('COM_REDSHOP_VOUCHER_CODE') . ' : ' . $arr_discount_type[1] . '<br>';
										}
									}

									if (!$discount_type)
									{
										$discount_type = JText::_('COM_REDSHOP_NO_DISCOUNT_AVAILABLE');
									}
									?>
									<?php echo $discount_type;?>

								</td>
							</tr>
							</tbody>
						</table>
						<input type="hidden" name="option" value="com_redshop"/>
						<input type="hidden" name="view" value="order"/>
						<input type="hidden" name="task" value="update_status"/>
						<input type="hidden" name="return" value="order_detail"/>
						<input type="hidden" name="order_id[]" value="<?php echo $order_id; ?>"/>
					</form>
				</div>
			</div>
		</div>

		<?php if ($this->detail->ship_method_id) : ?>
		<div class="col-sm-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo JText::_('COM_REDSHOP_SHIPPING_METHOD'); ?></h3>
				</div>
				<div class="box-body">
					<form action="index.php?option=com_redshop" method="post" name="updateshippingrate"
								  id="updateshippingrate">
						<table border="0" cellspacing="0" cellpadding="0" class="adminlist table table-striped table-condensed">
							<tr>
								<td align="left">
									<?php echo JText::_('COM_REDSHOP_SHIPPING_NAME') ?>:
								</td>
								<td>
									<?php  echo $shipping_name = $carthelper->replaceShippingMethod($this->detail, "{shipping_method}"); ?>
								</td>
							</tr>
							<tr>
								<td align="left">
									<?php echo JText::_('COM_REDSHOP_SHIPPING_RATE_NAME') ?>:
								</td>
								<td>
									<?php  echo $shipping_name = $carthelper->replaceShippingMethod($this->detail, "{shipping_rate_name}"); ?>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo JText::_('COM_REDSHOP_ORDER_SHIPPING_EXTRA_FILEDS'); ?>:
								</td>
								<td>
									<?php echo $ShippingExtrafields = $producthelper->getPaymentandShippingExtrafields($this->detail, 19); ?>
								</td>
							</tr>
							<tr>
								<td align="left">
									<?php echo JText::_('COM_REDSHOP_SHIPPING_MODE') ?>:
								</td>
								<td>
									<?php echo $this->loadTemplate('shipping'); ?>
								</td>
							</tr>
							<?php
							$details = RedshopShippingRate::decrypt($this->detail->ship_method_id);

							if (count($details) <= 1)
							{
								$details = explode("|", $row->ship_method_id);
							}

							$disp_style = '';

							if ($details[0] != 'plgredshop_shippingdefault_shipping_gls')
							{
								$disp_style = "style=display:none";
							}
							?>
							<tr>
								<td align="left">
									<div id="rs_glslocationId" <?php echo $disp_style?>>
									<?php echo $carthelper->getGLSLocation($shipping->users_info_id, 'default_shipping_gls', $this->detail->shop_id); ?>
									</div>
								</td>
							</tr>
						</table>
						<input type="submit" name="add" id="add" class="btn btn-primary"
							   value="<?php echo JText::_('COM_REDSHOP_UPDATE'); ?>"/>
						<input type="hidden" name="task" value="update_shippingrates">
						<input type="hidden" name="user_id" id="user_id"
							   value="<?php echo $this->detail->user_id; ?>">
						<input type="hidden" name="view" value="order_detail">
						<input type="hidden" name="return" value="order_detail">
						<input type="hidden" name="cid[]" value="<?php echo $order_id; ?>">
					</form>

				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>

	<div class="row">
		<div class="col-sm-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo JText::_('COM_REDSHOP_BILLING_ADDRESS_INFORMATION'); ?></h3>
					<?php if (!$tmpl)
					{ ?>
						<a class="joom-box btn btn-primary"
						   href="index.php?tmpl=component&option=com_redshop&view=order_detail&layout=billing&cid[]=<?php echo $order_id; ?>"
						   rel="{handler: 'iframe', size: {x: 500, y: 450}}"><?php echo JText::_('COM_REDSHOP_EDIT');?></a>
					<?php } ?>
				</div>
				<div class="box-body">
					<table class="adminlist table" border="0">
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_FIRSTNAME'); ?>:</td>
							<td><?php echo $billing->firstname; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_LASTNAME'); ?>:</td>
							<td><?php echo $billing->lastname; ?></td>
						</tr>
						<?php if ($is_company)
						{ ?>
							<tr>
								<td align="right"><?php echo JText::_('COM_REDSHOP_COMPANY'); ?>:</td>
								<td><?php echo $billing->company_name; ?></td>
							</tr>
						<?php } ?>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_ADDRESS'); ?>:</td>
							<td><?php echo $billing->address; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_ZIP'); ?>:</td>
							<td><?php echo $billing->zipcode; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_CITY'); ?>:</td>
							<td><?php echo $billing->city; ?></td>
						</tr>

						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_COUNTRY'); ?>:</td>
							<td><?php echo ($billing->country_code) ? JTEXT::_($order_functions->getCountryName($billing->country_code)) : ''; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_STATE'); ?>:</td>
							<td><?php echo ($billing->state_code) ? $order_functions->getStateName($billing->state_code, $billing->country_code) : ''; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_PHONE'); ?>:</td>
							<td><?php echo $billing->phone; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_EMAIL'); ?>:</td>
							<td>
								<a href="mailto:<?php echo $billing->user_email; ?>"><?php echo $billing->user_email; ?></a>
							</td>
						</tr>
						<?php
						if ($is_company)
						{
							?>
							<tr>
								<td align="right"><?php echo JText::_('COM_REDSHOP_VAT_NUMBER'); ?>:</td>
								<td><?php echo $billing->vat_number; ?></td>
							</tr>
							<tr>
								<td align="right"><?php echo JText::_('COM_REDSHOP_TAX_EXEMPT'); ?>:</td>
								<td><?php echo $billing->tax_exempt; ?></td>
							</tr>
							<tr>
								<td align="right"><?php echo JText::_('COM_REDSHOP_EAN_NUMBER'); ?>:</td>
								<td><?php echo $billing->ean_number; ?></td>
							</tr>
							<?php    $fields = $extra_field->list_all_field_display(8, $billing->users_info_id);
						}
						else
						{
							$fields = $extra_field->list_all_field_display(7, $billing->users_info_id);
						}
						echo $fields;
					?>
					</table>
				</div>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><?php echo JText::_('COM_REDSHOP_SHIPPING_ADDRESS_INFORMATION'); ?></h3>
					<?php if (!$tmpl)
					{ ?>
						<a class="joom-box btn btn-primary"
						   href="index.php?tmpl=component&option=com_redshop&view=order_detail&layout=shipping&cid[]=<?php echo $order_id; ?>"
						   rel="{handler: 'iframe', size: {x: 500, y: 450}}"><?php echo JText::_('COM_REDSHOP_EDIT');?></a>
					<?php } ?>
				</div>
				<div class="box-body">
					<table class="adminlist table" border="0">
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_FIRSTNAME'); ?>:</td>
							<td><?php echo $shipping->firstname; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_LASTNAME'); ?>:</td>
							<td><?php echo $shipping->lastname; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_ADDRESS'); ?>:</td>
							<td><?php echo $shipping->address; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_ZIP'); ?>:</td>
							<td><?php echo $shipping->zipcode; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_CITY'); ?>:</td>
							<td><?php echo $shipping->city; ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_COUNTRY'); ?>:</td>
							<td><?php echo JTEXT::_($order_functions->getCountryName($shipping->country_code)); ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_STATE'); ?>:</td>
							<td><?php echo $order_functions->getStateName($shipping->state_code, $shipping->country_code); ?></td>
						</tr>
						<tr>
							<td align="right"><?php echo JText::_('COM_REDSHOP_PHONE'); ?>:</td>
							<td><?php echo $shipping->phone; ?></td>
						</tr>
						<?php

						if ($is_company)
						{
							$fields = $extra_field->list_all_field_display(15, $shipping->users_info_id);
						}
						else
						{
							$fields = $extra_field->list_all_field_display(14, $shipping->users_info_id);
						}
						echo $fields; ?>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3><?php echo JText::_('COM_REDSHOP_ORDER_DETAILS'); ?></h3>
				</div>
				<div class="box-body">
					<table border="0" cellspacing="0" cellpadding="0" class="adminlist table table-striped table-condensed">
						<tbody>
							<tr>
								<td>
									<table border="0" cellspacing="0" cellpadding="0" class="adminlist" width="100%">
										<tr>
											<td>
												<table border="0" cellspacing="0" cellpadding="0" class="adminlist table table-striped table-condensed" width="100%">
													<tr>
														<th width="20%"><?php echo JText::_('COM_REDSHOP_PRODUCT_NAME'); ?></th>
														<th width="15%"><?php echo JText::_('COM_REDSHOP_ORDER_PRODUCT_NOTE'); ?></th>
														<th width="10%"><?php echo JText::_('COM_REDSHOP_PRODUCT_PRICE_WITHOUT_VAT'); ?></th>
														<th width="5%"><?php echo JText::_('COM_REDSHOP_TAX'); ?></th>
														<th width="10%"><?php echo JText::_('COM_REDSHOP_PRODUCT_PRICE'); ?></th>
														<th width="5%"><?php echo JText::_('COM_REDSHOP_PRODUCT_QTY'); ?></th>
														<th width="10%" align="right"><?php echo JText::_('COM_REDSHOP_TOTAL_PRICE'); ?></th>
														<th width="20%"><?php echo JText::_('COM_REDSHOP_STATUS'); ?></th>
														<th width="5%"><?php echo JText::_('COM_REDSHOP_ACTION'); ?></th>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
								<?php if ($totalDownloadProduct > 0) echo '<td>' . JText::_('COM_REDSHOP_DOWNLOAD_SETTING') . '</td>'; ?>
							</tr>
							<?php
								$ordervolume = 0;
								$cart = array();
								$subtotal_excl_vat = 0;
								for ($i = 0, $in = count($products); $i < $in; $i++)
								{
									$cart[$i]['product_id'] = $products[$i]->product_id;
									$cart[$i]['quantity'] = $products[$i]->product_quantity;
									$quantity = $products[$i]->product_quantity;
									$product_id = $products[$i]->product_id;

									if ($productdetail = $producthelper->getProductById($product_id))
									{
										$ordervolume = $ordervolume + $productdetail->product_volume;
									}

									$order_item_id = $products[$i]->order_item_id;
									$order_item_name = $products[$i]->order_item_name;
									$order_item_sku = $products[$i]->order_item_sku;
									$wrapper_id = $products[$i]->wrapper_id;

									$p_userfield = $producthelper->getuserfield($order_item_id);
									$subscribe_detail = $model->getUserProductSubscriptionDetail($order_item_id);
									$catId = $producthelper->getCategoryProduct($product_id);
									$res = $producthelper->getSection("category", $catId);
									$cname = '';

									if (count($res) > 0)
									{
										$cname = $res->category_name;
										$clink = JRoute::_($url . 'index.php?option=com_redshop&view=category&layout=detail&cid=' . $catId);
										$cname = "<a href='" . $clink . "'>" . $cname . "</a>";
									}

									$Product_name = $order_item_name . '<br>' . $order_item_sku . '<br>' . $p_userfield . '<br>' . $cname;

									$subtotal_excl_vat += $products[$i]->product_item_price_excl_vat * $quantity;
									$vat = ($products[$i]->product_item_price - $products[$i]->product_item_price_excl_vat);

									// Make sure this variable is object before we can use it
									if (is_object($productdetail))
									{
										// Generate frontend link
										$itemData = productHelper::getInstance()->getMenuInformation(0, 0, '', 'product&pid=' . $productdetail->product_id);
										$catIdMain = $productdetail->cat_in_sefurl;

										if (count($itemData) > 0)
										{
											$pItemid = $itemData->id;
										}
										else
										{
											$objhelper = redhelper::getInstance();
											$pItemid = $objhelper->getItemid($productdetail->product_id, $catIdMain);
										}

										$productFrontendLink  = JURI::root();
										$productFrontendLink .= 'index.php?option=com_redshop';
										$productFrontendLink .= '&view=product&pid=' . $productdetail->product_id;
										$productFrontendLink .= '&cid=' . $catIdMain;
										$productFrontendLink .= '&Itemid=' . $pItemid;
									}
									else
									{
										$productFrontendLink = '#';
									}

								?>
							<tr>
								<td>
									<table border="0" cellspacing="0" cellpadding="0" class="adminlist table table-striped table-condensed" width="100%">
										<tr>
											<td>
												<form action="index.php?option=com_redshop" method="post"
													name="itemForm<?php echo $order_item_id; ?>">
													<table border="0" cellspacing="0" cellpadding="0" class="adminlist table table-striped" width="100%">
														<tr>
															<td width="20%">
																<a href="<?php echo $productFrontendLink;?>" target="_blank">
																	<?php echo $Product_name;?>
																<a/>
															</td>
															<td width="15%">
																<?php
																	echo $products[$i]->product_attribute . "<br />" . $products[$i]->product_accessory . "<br/>" . $products[$i]->discount_calc_data;

																	if ($wrapper_id)
																	{
																	$wrapper = $producthelper->getWrapper($product_id, $wrapper_id);
																	echo "<br>" . JText::_('COM_REDSHOP_WRAPPER') . ": " . $wrapper[0]->wrapper_name . "(" . $products[$i]->wrapper_price . ")";
																	}

																	if ($subscribe_detail)
																	{
																	$subscription_detail = $model->getProductSubscriptionDetail($product_id, $subscribe_detail->subscription_id);
																	$selected_subscription = $subscription_detail->subscription_period . " " . $subscription_detail->period_type;
																	echo JText::_('COM_REDSHOP_SUBSCRIPTION') . ': ' . $selected_subscription;
																	}
																	?>
																<br/><br/>
																<?php
																	JPluginHelper::importPlugin('redshop_product');
																	$dispatcher = JDispatcher::getInstance();
																	$dispatcher->trigger('onDisplayOrderItemNote', array($products[$i]));
																	?>
															</td>
															<td width="10%">
																<div class="input-group">
																	<span class="input-group-addon"><?php echo Redshop::getConfig()->get('REDCURRENCY_SYMBOL'); ?></span>
																	<input type="text" name="update_price" id="update_price" class="form-control"
																		value="<?php echo $producthelper->redpriceDecimal($products[$i]->product_item_price_excl_vat); ?>"
																		size="10">
																</div>
															</td>
															<td width="5%"><?php echo Redshop::getConfig()->get('REDCURRENCY_SYMBOL') . " " . $vat;?></td>
															<td width="10%"><?php echo $producthelper->getProductFormattedPrice($products[$i]->product_item_price) . " " . JText::_('COM_REDSHOP_INCL_VAT'); ?></td>
															<td width="5%">
																<input type="text" name="quantity" id="quantity" class="col-sm-12"
																	value="<?php echo $quantity; ?>" size="3">
															</td>
															<td align="right" width="10%">
																<?php
																	echo Redshop::getConfig()->get('REDCURRENCY_SYMBOL') . "&nbsp;";
																	echo $producthelper->redpriceDecimal($products[$i]->product_final_price);
																	?>
															</td>
															<td width="20%">
																<?php
																	echo $order_functions->getstatuslist('status', $products[$i]->order_status, "class=\"inputbox\" size=\"1\" ");
																	?>
																<br><br><textarea cols="30" rows="3"
																	name="customer_note"><?php echo $products[$i]->customer_note;?></textarea><br/>
															</td>
															<td width="5%">
																<img class="delete_item"
																	src="<?php echo REDSHOP_FRONT_IMAGES_ABSPATH; ?>cross.png"
																	title="<?php echo JText::_('COM_REDSHOP_DELETE'); ?>"
																	alt="<?php echo JText::_('COM_REDSHOP_DELETE'); ?>"
																	onclick="if(confirm('<?php echo JText::_('COM_REDSHOP_CONFIRM_DELETE_ORDER_ITEM'); ?>')) { document.itemForm<?php echo $order_item_id; ?>.task.value='delete_item';document.itemForm<?php echo $order_item_id; ?>.submit();}">
																<img class="update_price"
																	src="<?php echo REDSHOP_FRONT_IMAGES_ABSPATH; ?>update.png"
																	title="<?php echo JText::_('COM_REDSHOP_UPDATE'); ?>"
																	alt="<?php echo JText::_('COM_REDSHOP_UPDATE'); ?>"
																	onclick="document.itemForm<?php echo $order_item_id; ?>.task.value='updateItem';document.itemForm<?php echo $order_item_id; ?>.submit();">
															</td>
														</tr>
													</table>
													<input type="hidden" name="task" id="task" value="">
													<input type="hidden" name="view" value="order_detail">
													<input type="hidden" name="productid" value="<?php echo $product_id; ?>">
													<input type="hidden" name="cid[]" value="<?php echo $order_id; ?>">
													<input type="hidden" name="order_id[]" value="<?php echo $order_id; ?>"/>
													<input type="hidden" name="order_item_id" value="<?php echo $order_item_id; ?>">
													<input type="hidden" name="return" value="order_detail"/>
													<input type="hidden" name="isproduct" value="1"/>
													<input type="hidden" name="option" value="com_redshop"/>
													<?php if ($tmpl)
														{ ?>
													<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>"/>
													<?php } ?>
												</form>
											</td>
											<?php
												$downloadarray = @$dproducts[$product_id];
												if ($totalDownloadProduct > 0)
												{
												?>
											<td>
												<?php
													if (count($downloadarray) > 0)
													{
													?>
												<form action="index.php?option=com_redshop" method="post"
													name="download_token<?php echo $order_item_id; ?>">
													<table cellpadding="0" cellspacing="0" border="0">
														<?php
															foreach ($downloadarray as $downloads)
															{
															$file_name = substr(basename($downloads->file_name), 11);
															$download_id = $downloads->download_id;
															$download_max = $downloads->download_max;
															$end_date = $downloads->end_date;
															$product_download_infinite = ($end_date == 0) ? 1 : 0;

															if ($end_date == 0)
															{
															$limit_over = false;
															}
															else
															{
															$days_in_time = $end_date - time();
															$hour = date("H", $end_date);
															$minite = date("i", $end_date);
															$days = round($days_in_time / (24 * 60 * 60));
															$limit_over = false;
															if ($days_in_time <= 0 || $download_max <= 0)
															{
															$limit_over = true;
															}
															}
															$td_style = ($end_date == 0) ? 'style="display:none;"' : 'style="display:table-row;"';
															?>
														<tr>
															<th colspan="2"
																align="center"><?php echo JText::_('COM_REDSHOP_TOKEN_ID') . ": " . $download_id;?></th>
														</tr>
														<?php
															if ($limit_over)
															{
															?>
														<tr>
															<td colspan="2"
																align="center"><?php echo JText::_('COM_REDSHOP_DOWNLOAD_LIMIT_OVER');?></td>
														</tr>
														<?php
															}
															?>
														<tr>
															<td valign="top" align="right"
																class="key"><?php echo JText::_('COM_REDSHOP_PRODUCT_DOWNLOAD_INFINITE_LIMIT'); ?>
																:
															</td>
															<td><?php echo JHTML::_('select.booleanlist', 'product_download_infinite_' . $download_id, 'class="inputbox" onclick="hideDownloadLimit(this,\'' . $download_id . '\');" ', $product_download_infinite);?></td>
														</tr>
														<tr id="limit_<?php echo $download_id; ?>" <?php echo $td_style;?>>
															<td><?php echo JText::_('COM_REDSHOP_PRODUCT_DOWNLOAD_LIMIT_LBL');?></td>
															<td><input type="text" name="limit_<?php echo $download_id; ?>"
																value="<?php echo $download_max; ?>"></td>
														</tr>
														<tr id="days_<?php echo $download_id; ?>" <?php echo $td_style;?>>
															<td><?php echo JText::_('COM_REDSHOP_PRODUCT_DOWNLOAD_DAYS_LBL');?></td>
															<td>
																<input type="text" name="days_<?php echo $download_id; ?>" size="2"
																	maxlength="2" value="<?php echo $days; ?>">
															</td>
														</tr>
														<tr id="clock_<?php echo $download_id; ?>" <?php echo $td_style;?>>
															<td><?php echo JText::_('COM_REDSHOP_PRODUCT_DOWNLOAD_CLOCK_LBL');?></td>
															<td>
																<input type="text" name="clock_<?php echo $download_id; ?>" size="2"
																	maxlength="2" value="<?php echo $hour; ?>">:
																<input type="text" name="clock_min_<?php echo $download_id; ?>"
																	size="2" maxlength="2" value="<?php echo $minite; ?>">
															</td>
														</tr>
														<tr>
															<td colspan="2">
																<input type="hidden" name="download_id[]"
																	value="<?php echo $download_id; ?>">
															</td>
														</tr>
														<?php
															}
															?>
														<tr>
															<td colspan="2" align="center">
																<input type="button" name="update"
																	value="<?php echo JText::_('COM_REDSHOP_UPDATE'); ?>"
																	onclick="document.download_token<?php echo $order_item_id; ?>.submit();">
																<input type="hidden" name="option" value="com_redshop"/>
																<input type="hidden" name="view" value="order"/>
																<input type="hidden" name="task" value="download_token"/>
																<input type="hidden" name="product_id"
																	value="<?php echo $product_id; ?>"/>
																<input type="hidden" name="return" value="order_detail"/>
																<input type="hidden" name="cid[]" value="<?php echo $order_id; ?>"/>
																<?php if ($tmpl)
																	{ ?>
																<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>"/>
																<?php } ?>
															</td>
														</tr>
													</table>
												</form>
												<?php
													}
													?>
											</td>
											<?php
												}
												?>
										</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<?php
								}
								$cart['idx'] = count($cart);
								$session->set('cart', $cart); ?>
							<tr>
								<td>
									<table align="right" border="0" cellspacing="0" cellpadding="0" class="adminlist table-striped table-condensed">
										<tbody>
											<tr align="left">
												<td align="right" width="65%"><strong><?php echo JText::_('COM_REDSHOP_ORDER_SUBTOTAL'); ?>:</strong>
												</td>
												<td align="right" width="35%">
													<?php echo $producthelper->getProductFormattedPrice($subtotal_excl_vat);?>
												</td>
											</tr>
											<tr align="left">
												<td align="right" width="65%"><strong><?php echo JText::_('COM_REDSHOP_ORDER_TAX'); ?>:</strong></td>
												<?php
													$order_tax               = $this->detail->order_tax;
													$totaldiscount           = $this->detail->order_discount;
													$special_discount_amount = $this->detail->special_discount_amount;
													$vatOnDiscount           = false;

													if ((int) Redshop::getConfig()->get('APPLY_VAT_ON_DISCOUNT') == 0 && (float) Redshop::getConfig()->get('VAT_RATE_AFTER_DISCOUNT')
													&& (int) $this->detail->order_discount != 0 && (int) $order_tax
													&& !empty($this->detail->order_discount))
													{
													$vatOnDiscount = true;
													$Discountvat   = ((float) Redshop::getConfig()->get('VAT_RATE_AFTER_DISCOUNT') * $totaldiscount) / (1 + (float) Redshop::getConfig()->get('VAT_RATE_AFTER_DISCOUNT'));
													$totaldiscount = $totaldiscount - $Discountvat;
													}

													if ((int) Redshop::getConfig()->get('APPLY_VAT_ON_DISCOUNT') == 0 && (float) Redshop::getConfig()->get('VAT_RATE_AFTER_DISCOUNT')
													&& (int) $this->detail->special_discount_amount != 0 && (int) $order_tax
													&& !empty($this->detail->special_discount_amount))
													{
													$vatOnDiscount           = true;
													$Discountvat             = ((float) Redshop::getConfig()->get('VAT_RATE_AFTER_DISCOUNT') * $special_discount_amount) / (1 + (float) Redshop::getConfig()->get('VAT_RATE_AFTER_DISCOUNT'));
													$special_discount_amount = $special_discount_amount - $Discountvat;
													}

													if ($vatOnDiscount)
													{
													$order_tax = (float) Redshop::getConfig()->get('VAT_RATE_AFTER_DISCOUNT') * ($subtotal_excl_vat - ($totaldiscount + $special_discount_amount));
													}
													?>
												<td align="right" width="35%">
													<?php echo $producthelper->getProductFormattedPrice($order_tax);?>
												</td>
											</tr>
											<tr align="left">
												<td align="right" width="65%">
													<strong>
													<?php
														if ($this->detail->payment_oprand == '+')
															echo JText::_('COM_REDSHOP_PAYMENT_CHARGES_LBL');
														else
															echo JText::_('COM_REDSHOP_PAYMENT_DISCOUNT_LBL');
														?>:
													</strong>
												</td>
												<td align="right" width="35%">
													<?php echo $producthelper->getProductFormattedPrice($this->detail->payment_discount); ?>
												</td>
											</tr>
											<tr align="left">
												<td align="right" width="65%">
													<strong>
													<?php echo JText::_('COM_REDSHOP_ORDER_DISCOUNT'); ?>:
													</strong>
												</td>
												<td align="right" width="35%">
													<form action="index.php?option=com_redshop" method="post"
														name="update_discount<?php echo $order_id; ?>">
														<div class="input-group">
															<span class="input-group-addon"><?php echo Redshop::getConfig()->get('REDCURRENCY_SYMBOL'); ?></span>
															<input type="text" name="update_discount"
																id="update_discount" class="form-control"
																value="<?php echo $producthelper->redpriceDecimal($this->detail->order_discount); ?>"
																size="10">
															<span class="input-group-addon">
															<a href="#" onclick="document.update_discount<?php echo $order_id; ?>.submit();">
															<?php echo JText::_('COM_REDSHOP_UPDATE'); ?>
															</a>
															</span>
														</div>
														<br />
														<?php echo $producthelper->getProductFormattedPrice($totaldiscount);?>
														<input type="hidden" name="task" value="update_discount">
														<input type="hidden" name="view" value="order_detail">
														<input type="hidden" name="cid[]" value="<?php echo $order_id; ?>">
													</form>
												</td>
											</tr>
											<tr align="left">
												<td align="right" width="65%"><strong><?php echo JText::_('COM_REDSHOP_SPECIAL_DISCOUNT'); ?>:</strong>
												</td>
												<td align="right" width="35%">
													<form action="index.php?option=com_redshop" method="post"
														name="special_discount<?php echo $order_id; ?>">
														<div class="input-group">
															<span class="input-group-addon">%&nbsp;</span>
															<input type="text" name="special_discount"
																id="special_discount" class="form-control"
																value="<?php echo $this->detail->special_discount; ?>"
																size="10">
															<span class="input-group-addon">
															<a href="#" onclick="document.special_discount<?php echo $order_id; ?>.submit();">
															<?php echo JText::_('COM_REDSHOP_UPDATE'); ?>
															</a>
															</span>
														</div>
														<br />
														<?php
															echo $producthelper->getProductFormattedPrice($special_discount_amount);
															?>
														<input type="hidden" name="order_total" value="<?php echo $this->detail->order_total; ?>">
														<input type="hidden" name="task" value="special_discount">
														<input type="hidden" name="view" value="order_detail">
														<input type="hidden" name="cid[]" value="<?php echo $order_id; ?>">
													</form>
												</td>
											</tr>
											<tr align="left">
												<td align="right" width="65%"><strong><?php echo JText::_('COM_REDSHOP_ORDER_SHIPPING'); ?>:</strong>
												</td>
												<td align="right" width="35%">
													<?php echo $producthelper->getProductFormattedPrice($this->detail->order_shipping);?>
												</td>
											</tr>
											<tr align="left">
												<td align="right" width="65%"><strong><?php echo JText::_('COM_REDSHOP_ORDER_TOTAL'); ?>:</strong></td>
												<td align="right" width="35%">
													<?php echo $producthelper->getProductFormattedPrice($this->detail->order_total);?>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3><?php echo JText::_('COM_REDSHOP_ADD_PRODUCT'); ?></h3>
				</div>
				<div class="box-body">
					<form action="index.php?option=com_redshop" method="post" name="adminFormAdd" id="adminFormAdd">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="adminlist table table-condensed table-striped">
							<tr>
								<th width="30%"><?php echo JText::_('COM_REDSHOP_PRODUCT_NAME'); ?></th>
								<th width="20%"><?php echo JText::_('COM_REDSHOP_ORDER_PRODUCT_NOTE'); ?></th>
								<th width="10%"><?php echo JText::_('COM_REDSHOP_PRODUCT_PRICE_WITHOUT_VAT'); ?></th>
								<th width="10%" align="right"><?php echo JText::_('COM_REDSHOP_TAX'); ?></th>
								<th width="10%" align="right"><?php echo JText::_('COM_REDSHOP_PRODUCT_PRICE'); ?></th>
								<th width="5%"><?php echo JText::_('COM_REDSHOP_PRODUCT_QTY'); ?></th>
								<th width="10%" align="right"><?php echo JText::_('COM_REDSHOP_TOTAL_PRICE'); ?></th>
								<th width="5%"><?php echo JText::_('COM_REDSHOP_ACTION');?></th>
							</tr>
							<tr id="trPrd1">
								<td><?php
									echo JHTML::_('redshopselect.search', '', 'product1',
										array(
											'select2.ajaxOptions' => array('typeField' => ', isproduct:1'),
											'select2.options' => array(
												'events' => array('select2-selecting' => 'function(e) {
													document.getElementById(\'product1\').value = e.object.id;
													displayProductDetailInfo(\'product1\', 0);
													displayAddbutton(e.object.id, \'product1\');}')
											)
										)
									);
									?>
									<div id="divAttproduct1"></div>
									<div id="divAccproduct1"></div>
									<div id="divUserFieldproduct1"></div>
								</td>
								<td id="tdnoteproduct1"></td>
								<td><input type="hidden" name="change_product_tmp_priceproduct1"
										   id="change_product_tmp_priceproduct1" value="0" size="10">
									<input type="text" name="prdexclpriceproduct1" style="display: none;" id="prdexclpriceproduct1" class="col-sm-12"
										   onchange="changeOfflinePriceBox('product1');" value="0" size="10"></td>
								<td align="right">
									<div id="prdtaxproduct1"></div>
									<input name="taxpriceproduct1" id="taxpriceproduct1" type="hidden" value="0"/></td>
								<td align="right">
									<div id="prdpriceproduct1"></div>
									<input name="productpriceproduct1" id="productpriceproduct1" type="hidden" value="0"/></td>
								<td><input type="text" name="quantityproduct1" id="quantityproduct1" style="display: none;"
										   onchange="changeOfflineQuantityBox('product1');" value="1" class="col-sm-12"
										   size="<?php echo Redshop::getConfig()->get('DEFAULT_QUANTITY'); ?>" maxlength="<?php echo Redshop::getConfig()->get('DEFAULT_QUANTITY'); ?>"></td>
								<td align="right">
									<div id="tdtotalprdproduct1"></div>
									<input name="subpriceproduct1" id="subpriceproduct1" type="hidden" value="0"/>

									<input type="hidden" name="main_priceproduct1" id="main_priceproduct1" value="0"/>
									<input type="hidden" name="tmp_product_priceproduct1" id="tmp_product_priceproduct1" value="0">
									<input type="hidden" name="product_vatpriceproduct1" id="product_vatpriceproduct1" value="0">
									<input type="hidden" name="tmp_product_vatpriceproduct1" id="tmp_product_vatpriceproduct1"
										   value="0">
									<input type="hidden" name="wrapper_dataproduct1" id="wrapper_dataproduct1" value="0">
									<input type="hidden" name="wrapper_vatpriceproduct1" id="wrapper_vatpriceproduct1" value="0">

									<input type="hidden" name="accessory_dataproduct1" id="accessory_dataproduct1" value="0">
									<input type="hidden" name="acc_attribute_dataproduct1" id="acc_attribute_dataproduct1"
										   value="0">
									<input type="hidden" name="acc_property_dataproduct1" id="acc_property_dataproduct1" value="0">
									<input type="hidden" name="acc_subproperty_dataproduct1" id="acc_subproperty_dataproduct1"
										   value="0">
									<input type="hidden" name="accessory_priceproduct1" id="accessory_priceproduct1" value="0">
									<input type="hidden" name="accessory_vatpriceproduct1" id="accessory_vatpriceproduct1"
										   value="0">

									<input type="hidden" name="attribute_dataproduct1" id="attribute_dataproduct1" value="0">
									<input type="hidden" name="property_dataproduct1" id="property_dataproduct1" value="0">
									<input type="hidden" name="subproperty_dataproduct1" id="subproperty_dataproduct1" value="0">
									<input type="hidden" name="requiedAttributeproduct1" id="requiedAttributeproduct1" value="0">
									<?php if ($tmpl)
									{ ?>
										<input type="hidden" name="tmpl" id="tmpl" value="<?php echo $tmpl ?>">
									<?php } ?>

								</td>
								<td><input type="button" class="btn btn-primary" name="add" id="add" style="display: none;"
										   value="<?php echo JText::_('COM_REDSHOP_ADD'); ?>"
										   onclick="javascript:submitbutton('add',this.form);"/></td>
							</tr>

							<tr>
								<td colspan="8">
									<input type="hidden" name="task" value="">
									<input type="hidden" name="user_id" id="user_id" value="<?php echo $this->detail->user_id; ?>">
									<input type="hidden" name="view" value="order_detail">
									<input type="hidden" name="return" value="order_detail">
									<input type="hidden" name="cid[]" value="<?php echo $order_id; ?>">
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-sm-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3><?php echo JText::_('COM_REDSHOP_ORDER_STATUS_LOG'); ?></h3>
				</div>
				<div class="box-body">
					<table border="0" cellspacing="0" cellpadding="0" class="adminlist table table-striped table-condensed">
						<tr>
							<th width="5%" align="center"><b><?php echo JText::_('COM_REDSHOP_NUM');?></b></th>
							<th width="15%" align="center"><b><?php echo JText::_('COM_REDSHOP_MODIFIED_DATE');?></b>
							</th>
							<th width="20%" align="center"><b><?php echo JText::_('COM_REDSHOP_STATUS');?></b></th>
							<th width="20%" align="center"><b><?php echo JText::_('COM_REDSHOP_PAYMENT_STATUS');?></b>
							</th>
							<th width="40%" align="center"><b><?php echo JText::_('COM_REDSHOP_COMMENT');?></b></th>
						</tr>
						<?php
						for ($log = 0; $log < count($log_rec); $log++):
							$log_row = $log_rec[$log];
							?>
							<tr>
								<td width="5%" align="center"><?php echo ($log + 1); ?></td>
								<td width="15%"
									align="center"><?php echo $config->convertDateFormat($log_row->date_changed); ?></td>
								<td width="20%" align="center"><?php echo $log_row->order_status_name; ?></td>
								<td width="20%" align="center"><?php if ($log_row->order_payment_status != "")
									{
										echo  JText::_('COM_REDSHOP_PAYMENT_STA_' . strtoupper($log_row->order_payment_status));
									} ?></td>
								<td width="40%" align="center"><?php echo urldecode($log_row->customer_note); ?></td>
							</tr>
						<?php endfor;?>
					</table>
				</div>
			</div>
		</div>
	</div>

</div>
<?php echo $this->loadTemplate('plugin');?>
<div id="divCalc"></div>
<script type="text/javascript">
	function hideDownloadLimit(val, tid) {

		var downloadlimit = document.getElementById('limit_' + tid);
		var downloaddays = document.getElementById('days_' + tid);
		var downloadclock = document.getElementById('clock_' + tid);

		if (val.value == 1) {

			downloadlimit.style.display = 'none';
			downloaddays.style.display = 'none';
			downloadclock.style.display = 'none';
		} else {

			downloadlimit.style.display = 'table-row';
			downloaddays.style.display = 'table-row';
			downloadclock.style.display = 'table-row';
		}
	}
</script>
