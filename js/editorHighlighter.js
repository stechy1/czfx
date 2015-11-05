//create hightlight text area:
create_bind_textarea_highlight({ 
			eleId: 				"highlighter",										//<= div container id, make sure the container has a placer 
                                                                                    //   attribute or it will be set to random number.
 			width:				400,												//<= px, DEF: 300
			height:				110,												//<= px, DEF: 100
			padding:			5,													//<= px, DEF: 5
			background:			'white',											//<= DEF: white
			backgroundControls:	'#585858',											//<= DEF: #151515
			radius:				5,													//<= px, DEF: 5
			fontFamilly: 		'Arial',											//<= DEF: arial
			fontSize:			13,													//<= px, DEF: 13
			//lineHeight:		18,													//<= px, DEF: font size +5px
			counterlettres:		true,												//<= enable counter Def false
			counterFont:		'red',												//<= DEF: black
			matchpatterns:		[["(#[0-9A-Za-z]{0,})","$1"],["(@[0-9A-Za-z]{0,})","$1"]],	//<= patterns that should be highlighted - escape back slashes,
                                                                                            //   those patterns will highliht #someword,@somtext
			hightlightsColor:	['#00d2ff','#FFBF00'],										//<= highlight colors for patterns according index DEF if not
                                                                                            //   set is YELLOW.
			objectsCopy:		["copy_hashes","copy_at"]									//<= Optional containers id that should copy matches according
                                                                                            //   index.
			//PRESS Ctrl + SHIFT fordirection swip!
		});	


//build function -> u can create as many as you want !
function create_bind_textarea_highlight(optionsHigh)
{
		if (optionsHigh.eleId!= 'undefined'){
		//elements:
		var conn = $('#' + optionsHigh.eleId);
		var placer = $(conn).attr('placer');
		
		if (conn.length){
		if (placer != 'undefined'){
		
				//create elements:
				$(conn).html("<textarea id='text_" + placer + "'></textarea><div id='cloner_" + placer + "' class='copier'></div><p id='control_" + placer + "'>X</p>");
				
				var connTextArea = $('#' + optionsHigh.eleId).children('textarea');
				var cloner = $('#' + optionsHigh.eleId).children('div#cloner_' + placer);
				var controls = $('#' + optionsHigh.eleId).children('p');
				//default options:
					if (typeof optionsHigh.width == 'undefined') { optionsHigh.width=300; }
					if (typeof optionsHigh.height == 'undefined') { optionsHigh.height=100; }
					if (typeof optionsHigh.padding == 'undefined') { optionsHigh.padding=5; }
					if (typeof optionsHigh.fontFamilly == 'undefined') { optionsHigh.fontFamilly='Arial'; }
					if (typeof optionsHigh.background == 'undefined') { optionsHigh.background = 'white'; }
					if (typeof optionsHigh.radius == 'undefined') { optionsHigh.radius = 5; }
					if (typeof optionsHigh.fontSize == 'undefined') { optionsHigh.fontSize = 13; }
					if (typeof optionsHigh.lineHeight == 'undefined') { optionsHigh.lineHeight = optionsHigh.fontSize + 5; }
					if (typeof optionsHigh.backgroundControls == 'undefined') { optionsHigh.backgroundControls = "#151515"; }
					if (typeof optionsHigh.counterFont == 'undefined') { optionsHigh.counterFont = 'black'; }
				
				//if conter for letters is set create element:
				if (typeof optionsHigh.counterlettres!='undefined') { if (optionsHigh.counterlettres!=false) { $(conn).append('<div id="counter_' + placer +'">Chars count: 0</div>') 
					var counter = $('#' + optionsHigh.eleId).children('div#counter_' + placer ); } }
				
				//css append:
				$(conn).css({ 'position':'relative','background-color':'transparent','border':'0px','width':(optionsHigh.width+30) + 'px','height':(optionsHigh.height+28) + 'px'});
				$(connTextArea).css({
										'direction':		'ltr',
										'position':         'absolute',
										'padding':      	optionsHigh.padding + 'px',
										'left':             '0',
										'top':              '0',
										'overflow':     	'hidden',
										'border':           '1px solid #595959',
										'background':		'transparent',
										'width':            (optionsHigh.width-1) + 'px',
										'height':           (optionsHigh.height-1) + 'px',
										'cursor':           'text',
										'font-size':        optionsHigh.fontSize + 'px',
										'font-family':      optionsHigh.fontFamilly,
										'line-height':      optionsHigh.lineHeight + 'px',
										'word-wrap': 		'break-word',
										'white-space':      'pre-wrap',
										'resize':           'none',
										'border-radius':	optionsHigh.radius + 'px',
										'z-index':      	'5'
									});
				$(cloner).css({
										'direction':		'ltr',
										'position':         'absolute',
										'padding':      	optionsHigh.padding + 'px',
										'left':             '0',
										'top':              '0',
										'overflow':     	'hidden',
										'border':           'none',
										'background':		optionsHigh.background,
										'width':            optionsHigh.width + 'px',
										'height':           optionsHigh.height + 'px',
										'font-size':        optionsHigh.fontSize + 'px',
										'font-family':      optionsHigh.fontFamilly,
										'white-space':      'pre-wrap',
										'line-height':      optionsHigh.lineHeight + 'px',
										'word-wrap': 		'break-word',
										'border-radius':	optionsHigh.radius + 'px',
										'color':			optionsHigh.background,
										'z-index':      	'4'
									});
				$(counter).css({
										'position':			'absolute',
										'font-family':      'Arial',
										'font-size':        '11px',
										'font-weight':      'bold',
										'color':			optionsHigh.counterFont,
										'border':			'0px',
										'left': 			'0',
										'top':              (optionsHigh.height+8) + 'px',
										'text-align':		'left',
										'padding':          '3px 5px 3px 5px',
										'margin':           '0',
										'background':		'transparent',
										'border-radius':	'0px',
										'z-index':      	'3'
								});
				$(controls).css({
										'position':			'absolute',
										'font-family':      'Arial',
										'font-size':        '12px',
										'font-weight':      'bold',
										'color':			'white',
										'border':			'0px',
										'cursor':			'pointer',
										'left': 			optionsHigh.width+12 + 'px',
										'top':              '0',
										'padding':          '3px 5px 3px 5px',
										'margin':           '0',
										'background':		optionsHigh.backgroundControls,
										'border-radius':	'5px',
										'z-index':      	'6',
										'opacity':			'0.5'
								});
				//controls events:
				$(controls).mouseover(function(){ $(this).css({'opacity':'1'}); }).mouseout(function(){ $(this).css({'opacity':'0.5'}); });
				$(controls).click(function(){ $(connTextArea).val(''); $(cloner).empty();
					if (typeof optionsHigh.counterlettres!='undefined') { if (optionsHigh.counterlettres!=false) { $(counter).text('Chars count: 0'); }}
					if (typeof optionsHigh.objectsCopy != 'undefined') { for(var i=0; i<optionsHigh.objectsCopy.length; i++){ $('#'+optionsHigh.objectsCopy[i]).empty(); } }
				});
				
				//ctrl shift:
				
				//bind the textarea and the div:
				$(connTextArea).keyup(function(e){
					if (e.keyCode == 17 && e.shiftKey) {  
						if($(connTextArea).css('direction')=='ltr') { console.log('rtl'); $(cloner).css({'direction':'rtl'}); $(connTextArea).css({'direction':'rtl'}); }
						else { $(cloner).css({'direction':'ltr'}); $(connTextArea).css({'direction':'ltr'}); console.log('ltr');}
					}
					var parse_text = $(this).val();
					var parse_text_match = parse_text;
					//counter if its true:
						if (typeof optionsHigh.counterlettres!='undefined') { if (optionsHigh.counterlettres!=false) { $(counter).text('Chars count: ' + parse_text.length); }}
					var span_style = "padding:0; height:" + (optionsHigh.fontSize+3) +"px; margin:0; font-size:" + optionsHigh.fontSize + "px; font-family: " + optionsHigh.fontFamilly + "; white-space:pre-wrap; color:white; border-radius:3px; background-color:";
					for (var i=0; i<optionsHigh.matchpatterns.length; i++)
						{
							//search patterns and wrap with styled <span>
							var patt = new RegExp(optionsHigh.matchpatterns[i][0],"g");
							if (typeof optionsHigh.matchpatterns[i][1] != 'undefined' ) var replace = optionsHigh.matchpatterns[i][1]; else var replace = "$1";
							if (typeof optionsHigh.hightlightsColor != 'undefined' ) { if (typeof optionsHigh.hightlightsColor[i] != 'undefined' ) { var color_high = optionsHigh.hightlightsColor[i]; } else { var color_high = 'yellow'; }  } else { var color_high = 'yellow'; }
							parse_text = parse_text.replace(patt, "<span style='" + span_style + color_high + "; color:" + color_high + ";'>" + replace + "</span>");
							//search patterns and copy to write container if its set
							if (typeof optionsHigh.objectsCopy != 'undefined' ){
								if (typeof optionsHigh.objectsCopy[i] != 'undefined' ){
									var res = parse_text_match.match(patt);
									var strres = "";
									if (res != null) { for (var k=0; k<res.length; k++) { strres = strres + ' ' + res[k]; if (k==res.length-1){ $('#'+ optionsHigh.objectsCopy[i]).text(strres); } } }
								}
							}
						}
					
					$(cloner).html(parse_text);
	
				});
		}else{ $(conn).text('please ad aunique placer value');}
		}else{console.log('no container <div> found please pass the correct id name');}
		}else{console.log('no container set please pass the id threw - create_bind_textarea_highlight({eleId: ID}); ');}
}