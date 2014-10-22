<span style="float:left;padding: 0 10px 0 10px">-</span>
<select id="sms_to_duration_minutes" name="sms_to_duration_minutes" class="cmf-skinned-select cmf-skinned-text" style="float:left">
	{html_options values=$ns.values selected=$ns.user->getSmsToDurationMinutes() output=$ns.timesDisplayNames}
</select>
