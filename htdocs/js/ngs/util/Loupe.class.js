/**
* @author Vahagn Sookiasian
* @e-mail lyov@live.com , lyov@lyov.info
* @homePage lyov.info
* @copyright 2008
*/
var Loupe = Class.create({
	initialize: function(imgSmall, loupePrewiePanel, loupePanel){

		if (imgSmall.id) {

			this.imgSmall = imgSmall;
		}
		else {

			this.imgSmall = $(imgSmall);

		}
		if (loupePrewiePanel.id) {

			this.loupePrewiePanel = loupePrewiePanel;
		}
		else {

			this.loupePrewiePanel = $(loupePrewiePanel);

		}

		if (loupePanel.id) {

			this.loupePanel = loupePanel;
		}
		else {

			this.loupePanel = $(loupePanel);

		}
		Element.setOpacity(this.loupePanel, 0.3);
		this.loupeImage = $("loupeImage");

		var loupeWidth = this.loupeImage.clientWidth;
		var loupeHeight = this.loupeImage.clientHeight;

		var widthConf = $("origPhtotWidth").value/this.loupeImage.clientWidth*10;
		var heightConf = $("origPhtotHeight").value/this.loupeImage.clientHeight*10;



		this.imgSmallHeight = this.imgSmall.getDimensions()['height'];
		this.imgSmallWidth = this.imgSmall.getDimensions()['width'];


		this.widthConf = widthConf/10;
		this.heightConf = heightConf/10;

		this.previwPanWidth = 400;
		this.previwPanHeight = 400;



	},

	changeLoupePlace: function(event){

		if((event.clientX-20-(this.loupePanelWidth/2))<0){

			var loupePanXplace = 0;


			}else if((event.clientX-20+(this.loupePanelWidth/2))> this.loupeImage.getDimensions()['width']){

				var loupePanXplace = this.loupeImage.getDimensions()['width']-this.loupePanelWidth;

				}else{

					var loupePanXplace = event.clientX-20-(this.loupePanelWidth/2);
				}

				if((event.clientY-140-(this.loupePanelHeight/2))<0){

					var loupePanYplace = 0;

					}else if((event.clientY-140+(this.loupePanelHeight/2))> this.loupeImage.getDimensions()['height']){

						var loupePanYplace = this.loupeImage.getDimensions()['height']-this.loupePanelHeight;

						}else{

							var loupePanYplace = event.clientY-140-(this.loupePanelHeight/2);
						}





						this.loupePanel.style.top = loupePanYplace+"px";
						this.loupePanel.style.left = loupePanXplace+"px";
						this.loupePrewiePanel.style.backgroundPosition = '-' + parseInt((loupePanXplace * this.widthConf)) + 'px -' + parseInt((loupePanYplace * this.heightConf)) + 'px ';



					},

					start: function(){

						this.loupePanel.style.width = parseInt(this.previwPanWidth/this.widthConf)+"px";
						this.loupePanel.style.height = parseInt(this.previwPanHeight/this.heightConf)+"px";

						this.loupePanelHeight = this.loupePanel.getDimensions()['height'];
						this.loupePanelWidth = this.loupePanel.getDimensions()['width'];

						this.loupeImage.onclick = this.changeLoupePlace.bindAsEventListener(this);

						new Draggable(this.loupePanel, {
							snap: function(x, y, draggable){

								function constrain(n, lower, upper){
									if (n > upper)
									return upper;
									else
										if (n < lower)
										return lower;
										else
											return n;
										}




										element_dimensions = Element.getDimensions(draggable.element);
										parent_dimensions = Element.getDimensions(draggable.element.parentNode);

										if(x<0){
											x=0;
										}
										else if(x+element_dimensions.width > parent_dimensions.width){
											x = parent_dimensions.width-element_dimensions.width;
										}

										if(y<0){
											y=0;
										}
										else if(y+element_dimensions.height > parent_dimensions.height){
											y = parent_dimensions.height-element_dimensions.height;
										}

										if ((x+this.loupePanelWidth) <= (this.imgSmallWidth) && (y+this.loupePanelHeight) <= (this.imgSmallHeight) && x >= -2 && y >= -2) {

											this.loupePrewiePanel.style.backgroundPosition = '-' + parseInt((x * this.widthConf)) + 'px -' + parseInt((y * this.heightConf)) + 'px ';

										}
										$("outbox").value = "x:"+x+"px; y="+y+"px";
										return [constrain(x, 0, parent_dimensions.width - element_dimensions.width), constrain(y, 0, parent_dimensions.height - element_dimensions.height)];
									}.bind(this)

								});



							}


						});












