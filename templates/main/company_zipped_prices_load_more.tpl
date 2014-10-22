
{foreach from=$ns.groupCompaniesZippedPricesByCompanyId item=pr}
	<a  style="display: inline-block;white-space: nowrap;margin-left:5px" href="{$STATIC_PATH}/price/zipped_price_unzipped/{$pr->getId()}"> 
		<img style="float:left" src = "{$SITE_PATH}/img/file_types_icons/zip_icon.png"  alt="zip"/>
		<div style="clear: both"></div>
		{assign var="uploadDateTime" value = $pr->getUploadDateTime()}					
		<span style="word-wrap: break-word;float:left;"> 
			{if $uploadDateTime}
				{$uploadDateTime|date_format:"%m/%d"}
				<br />
				{$uploadDateTime|date_format:"%H:%M"}												
			{/if} </span> 
	</a>
{/foreach}

