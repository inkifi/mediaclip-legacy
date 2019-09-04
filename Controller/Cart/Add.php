<?php
namespace Mangoit\MediaclipHub\Controller\Cart;
// 2019-09-04
class Add extends \Magento\Checkout\Controller\Cart\Add {
	/** 2019-09-04 */
	function execute() {
		$params = $this->getRequest()->getParams();
		try {
			if (isset($params['qty'])) {
				$filter = new \Zend_Filter_LocalizedToNormalized(
					['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
				);
				$params['qty'] = $filter->filter($params['qty']);
			}
			$product = $this->_initProduct();
			$related = $this->getRequest()->getParam('related_product');
			if (!$product) {
				return $this->goBack();
			}
			// 2019-09-04 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// «Port a Shopify / React.js product designer app to Magento 2»:
			// https://www.upwork.com/ab/f/contracts/22344597
			if (ikf_is_mediaclip_product($product)) {
				$session =   $this->_objectManager->get('Magento\Customer\Model\Session');
				$session->setMediaclip($params);
				$editorUrl = mc_h()->getEditorLinkUrl($product, $params);
				$this->getResponse()->setRedirect($editorUrl);
			}
			else {
				$this->cart->addProduct($product, $params);
				if (!empty($related)) {
					$this->cart->addProductsByIds(explode(',', $related));
				}
				$this->cart->save();
				/**
				 * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
				 */
				$this->_eventManager->dispatch(
					'checkout_cart_add_product_complete',
					['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
				);
				if (!$this->_checkoutSession->getNoCartRedirect(true)) {
					if (!$this->cart->getQuote()->getHasError()) {
						$message = __(
							'You added %1 to your shopping cart.',
							$product->getName()
						);
						$this->messageManager->addSuccessMessage($message);
					}
					return $this->goBack(null, $product);
				}
			}
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			if ($this->_checkoutSession->getUseNotice(true)) {
				$this->messageManager->addNotice(
					$this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
				);
			} else {
				$messages = array_unique(explode("\n", $e->getMessage()));
				foreach ($messages as $message) {
					$this->messageManager->addError(
						$this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
					);
				}
			}

			$url = $this->_checkoutSession->getRedirectUrl(true);

			if (!$url) {
				$cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
				$url = $this->_redirect->getRedirectUrl($cartUrl);
			}

			return $this->goBack($url);

		} catch (\Exception $e) {
			$this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
			$this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
			return $this->goBack();
		}
	}
}
