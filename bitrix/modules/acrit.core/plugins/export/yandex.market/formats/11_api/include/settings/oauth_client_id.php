<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][OAUTH_CLIENT_ID]" value="<?=htmlspecialcharsbx($this->arParams['OAUTH_CLIENT_ID']);?>"
		data-role="acrit_exp_yandex_market_api_oauth_client_id" size="36" maxlength="32" />
</div>