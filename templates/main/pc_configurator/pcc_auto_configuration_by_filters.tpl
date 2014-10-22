<div style="border-top: 1px solid #666">
	<div style="text-align: center;padding: 10px;">
		<h4>{$ns.langManager->getPhraseSpan(385)}</h4>
	</div>
	<form id="pcc_auto_conf_form">
		<div style="font-size: 12px">
			<div style="vertical-align: middle;padding: 10px;">
				<label for="total_price" >{$ns.langManager->getPhraseSpan(313)}</label>
				<input id="total_price" type="text"  style="width: 100px;margin: 0 10px 0 10px;"/>
				Դր.
			</div>
			<div style="vertical-align: middle;padding: 10px;">
				<label for="is_gaming" style="float:left;width: 200px">{$ns.langManager->getPhraseSpan(383)}</label>
				<input id="is_gaming" type="checkbox" style="margin: 0 10px 0 10px;" />
			</div>
			<div style="vertical-align: middle;padding: 10px;">
				<label for="only_case" style="float:left;width: 200px">{$ns.langManager->getPhraseSpan(384)}</label>
				<input id="only_case" type="checkbox" style="margin: 0 10px 0 10px;"  />
			</div>
			<div style="text-align: center ;margin: 40px 0 10px 0">
				<button id="suggest_pc_button" class="button1 green">
					{$ns.langManager->getPhraseSpan(382)}
				</button>
			</div>
		</div>
	</form>
</div>