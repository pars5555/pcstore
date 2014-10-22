<div style="position: relative;width: 100%;height:100%">
	<div id = 'tree_wrapper' style="width: {$ns.ItemCategoriesLeftBarWidth}px;height:100%;float:left;background: #F7F7F7;overflow: auto">
		{$ns.treeView->display(true)}
	</div>
	<div id='right_side_body' style='height:100%;width:{$ns.ItemCategoriesRightBarWidth-10}px;position:relative;float:left;'>

		<div id = 'edit_category_attributes_div'>

			<div class="box-header">
				<h1>Category Attributes</h1>
			</div>
			<div style="width: 100%;">
				<form id="category_attributes_form" name="category_attributes_form" style="padding: 15px;">
					<p>
					<div style=" float: left;width:110px;text-align: left;line-height:22px">
						<label for="title">Title</label>
					</div>
					<input name="title" type="text" size="30" style="float: left"/> 
					<div id="selected_category_id" style="float: left;line-height: 20px;margin-left: 10px;"></div>
					</p>
					<p>												
					<div style="float: left;text-align: left;line-height:18px;margin-left: 20px;">
						<label for="last_clickable">Last Clickable</label>
						<input  name="last_clickable" type="checkbox" />
					</div>
					</p>

				</form>
				<div style="text-align: center">
					<button id="save_category_attributes_button" class="button blue" >
						save
					</button>
					<button id="reset_category_attributes_button" class="button glyph">
						reset
					</button>
				</div>
			</div>

		</div>
		<div style="top:200px; width:100%;height:{$ns.userProfileFooterDivHeight}px;position: absolute;">
			<div class="box-header" >
				<h1>Add/Remove Categoies</h1>
			</div>
			<div style="width: 100%;">
				<div style="padding: 15px;">

					<button id="add_category_button" class="button glyph">
						Add Category
					</button>
					<button id="remove_category_button" class="button glyph">
						Remove Category
					</button>

				</div>
				<div style="padding: 15px;">
					<button id="move_up_category_button" class="button glyph">
						&uarr;	Move UP
					</button>
					<button id="move_down_category_button" class="button glyph">
						&darr;	Move Down
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
