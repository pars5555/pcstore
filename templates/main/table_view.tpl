<div style="height: 80px;white-space: nowrap;overflow: auto">
	table: <select onkeyup="this.blur();
			this.focus();" class="cmf-skinned-select cmf-skinned-text" id='tv_table_name_select'>
		{html_options values=$ns.all_table_names selected=$ns.table_name output=$ns.all_table_names_uf}
	</select>
	rows per page: <select onkeyup="this.blur();
			this.focus();" class="cmf-skinned-select cmf-skinned-text" id='tv_rows_per_page'>
		{html_options values=$ns.table_view_per_page_options selected=$ns.rows_per_page output=$ns.table_view_per_page_options}
	</select>
	page: <select onkeyup="this.blur();
			this.focus();" class="cmf-skinned-select cmf-skinned-text" id='tv_page_options'>
		{html_options values=$ns.allPagesArray selected=$ns.page output=$ns.allPagesArray}
	</select>

	sort by: <select onkeyup="this.blur();
			this.focus();" class="cmf-skinned-select cmf-skinned-text" id='tv_sort_options'>
		{html_options options=$ns.tableColumnsNamesArrayForSorting selected=$ns.order_by_field_name}
	</select>
	ASC/DESC: <select onkeyup="this.blur();
			this.focus();" class="cmf-skinned-select cmf-skinned-text" id='tv_sort_asc_desc_options'>
		{html_options options=$ns.order_by_asc_desc_options selected=$ns.order_by_asc_desc}
	</select>
	<button href="javascript:void(0);"  id="tv_delete_selected_rows_button" disabled>Delete</button>	
	<button href="javascript:void(0);"  id="tv_reload_button"><img src="{$SITE_PATH}/img/reload_24x24.png"/></button>	
	<button href="javascript:void(0);"  id="tv_empty_table_button" title="Empty table!!!"><img src="{$SITE_PATH}/img/empty_24x24.png"/></button>	
	<button href="javascript:void(0);"  id="tv_search_button" title="Search"><img src="{$SITE_PATH}/img/search_24x24.png"/></button>	
	<input type="hidden" id="tv_search_text" value="{$ns.search_text}"/>
	<input type="hidden" id="tv_search_column_name" value="{$ns.search_column}"/>
		{if $ns.table_exists}
		<h1 style="text-align: center">{$ns.table_name_uf} ({$ns.totalRowsCount} rows)</h1>
	{else}
		{$ns.table_name} table is not exists!
	{/if}
</div>

<div style="overflow: auto;position: absolute;top: 150px;right: 10px;bottom: 10px;left: 10px;">
	<table style="width: 100%;" id="tv_table_view">
		<tbody>
			<tr>
				<th></th>
					{foreach from=$ns.tableColumnsNamesArray item=tc}
					<th style="text-align: left">
						{$tc}
					</th>
				{/foreach}
			</tr>
			{assign var="pk" value=$ns.table_pk_name}
			{foreach from=$ns.rowDtos item=rowDto name=rd}
				{if $smarty.foreach.rd.index % 10 == 0 && $smarty.foreach.rd.index>0}
					<tr>
						<th></th>
							{foreach from=$ns.tableColumnsNamesArray item=tc}
							<th style="text-align: left">
								{$tc}
							</th>
						{/foreach}
					</tr>
				{/if}

				<tr tv_table_pk_value='{$rowDto.$pk}'>
					<td style="padding: 5px;border: 1px solid #999"><input type="checkbox" class="tv_row_select_checkbox" row_index='{$smarty.foreach.rd.index+1}'/></td>
						{foreach from=$ns.tableColumns item=tc}
							{assign var="columnName" value =$tc.Field}
							{assign var="columnType" value =$tc.Type}
						<td style="max-width: 300px;text-overflow: ellipsis;overflow: hidden;padding: 5px;border: 1px solid #999" 
							columnType="{$columnType}" columnName="{$columnName}">{$rowDto.$columnName}</td>
					{/foreach}
				</tr>
			{/foreach}
			<tr>
				<td style="padding: 5px;border: 1px solid #999">*</td>
				{foreach from=$ns.tableColumns item=tc}
					{assign var="columnName" value =$tc.Field}
					{assign var="columnType" value =$tc.Type}
					<td style="max-width: 300px;text-overflow: ellipsis;overflow: hidden;padding: 5px;border: 1px solid #999" 
						columnType="{$columnType}" columnName="{$columnName}"></td>
				{/foreach}
			</tr>
		</tbody>
	</table>
</div>
<input type="hidden" id="tv_table_name" value="{$ns.table_name}"/>
<input type="hidden" id="tv_table_columns_joined" value="{$ns.tableColumnsNamesJoined}"/>