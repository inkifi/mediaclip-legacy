<?php
namespace Mangoit\MediaclipHub\Controller\Index;
use Df\Framework\W\Result\Json;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Mangoit\MediaclipHub\Session as mSession;
/**
 * 2018-09-11
 * https://doc.mediacliphub.com/pages/Getting%20Started/yourFirstIntegration.html#renew-token
 * «The renew-token page
 * The user token is short-lived.
 * If you leave the application open a long time, the session will expire.
 * To keep the "session" alive, the Mediaclip Designer makes periodic HTTP calls to a special `renew-token` page.
 * This special page must make an HTTP call to Mediaclip Hub to renew the user token.
 *
 * Create a new page (e.g. renew-token.php), in which you will do one of the following two scenarios:
 * a) If the user's session is still valid on your eCommerce website, you can renew the token
 * 		1.	Read the POST body, which looks like this JSON:
 * 				{"token": "some-very-long-and-cryptic-token"}
 * 		2.	Forward the data you received to Mediaclip Hub
 * 			in a POST call that contains your store's authorization header:
 * 				POST https://api.mediacliphub.com/auth/jwt/renew HTTP/1.1
 *				Content-Type: application/json
 *				Authorization: HubApi TVktS0VZOk1ZLVNFQ1JFVA==
 * 				{"token": "some-very-long-and-cryptic-token"}
 * 		3.	You will receive a new token in the JSON response:
 *				{
 *					"expirationUtc": "2015-01-15T10:14:00.0000000Z",
 *					"issueDateUtc": "2015-01-15T09:54:00.0000000Z",
 *					"token": "another-very-long-and-cryptic-token",
 *					"userId": "hub-user-id"
 *				}
 * 		4.	Return the complete JSON response in your response, with a application/json Content-Type.
 * b) If the user's session is no longer valid on your eCommerce website, redirect to login page.
 * If the store's session is not valid, return a redirect URL (e.g. the store's login page)
 * to force the user to re-login.
 * Do so by returning the following JSON:
 * 		{"redirectUrl": "http://store.example.com/login"}
 *
 * In order to test that service, the simplest way is to use a JavaScript helper function.
 * 1) Launch the Mediaclip Designer using what we have built so far.
 * 2) Open the Chrome/IE/Firefox developer console (F12).
 * 3) Select the "Console" tab
 * 4) Paste this into the console: window.mediaclip.hub.launcher.keepAlive();
 * 5) A request will immediately be made to your renew token page.
 * You can see the request and the response in the Network tab.
 *
 * Your session will now keep working for as long as you keep the page open.
 * See authorization (https://doc.mediacliphub.com/pages/Api/authorization.html)
 * for more information on how the session works.
 *
 * NOTE: For increased security, we recommend that you verify
 * that the token you receive in the `renew-token` page is indeed refreshed by the correct user.
 * You will need to store who owns the token (e.g. in a memory cache or temporary database),
 * and verify that the user (e.g. using the cookie/session) is still logged in, and owns that token.»
 *
 * 2019-05-10 https://doc.mediaclip.ca/hub/getting-started/launching-designer/#renew-token-endpoint
 */
class RenewMediaclipToken extends Action {
	/**
	 * 2018-09-11
	 * @return Json
	 */
	function execute() {
		try {
			$s = df_customer_session(); /** @var Session|mSession $s */
			/** @var object|array(string => mixed) $r */
			if (!($t = $s->getMediaClipToken())) { /** @var object|null $t */
				/**
				 * 2018-09-11
				 * «If the user's session is no longer valid on your eCommerce website, redirect to login page.
				 * If the store's session is not valid, return a redirect URL (e.g. the store's login page)
				 * to force the user to re-login.
				 * Do so by returning the following JSON:
				 * 		{"redirectUrl": "http://store.example.com/login"}
				 * https://doc.mediacliphub.com/pages/Getting%20Started/yourFirstIntegration.html#renew-token
				 * »
				 */
				$r = ['redirectUrl' => df_customer_url_h()->getLoginUrl()];
			}
			else {
				/**
				 * 2018-09-11
				 * «Read the POST body, which looks like this JSON:
				 * 		{"token": "some-very-long-and-cryptic-token"}
				 * »
				 * https://doc.mediacliphub.com/pages/Getting%20Started/yourFirstIntegration.html#renew-token
				 */
				$req = json_decode(@file_get_contents('php://input')); /** @var object $req */
				if ($t->token !== $req->token) {
					df_error("Magento token: «{$t->token}», Mediaclip token: «{$req->token}».");
				}
				$s->setMediaClipToken($r = mc_h()->RenewToken($req));
			}
		}
		catch (\Exception $e) {
			$r = ['error' => $e->getMessage()];
			ikf_log($e);
		}
		return Json::i($r);
	}
}