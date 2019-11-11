(function ($) {
    $.widget("ui.combify", {
        options: {
    		// capitalizeInput: false,
		maxLength: 0
        },
        _create: function () {
            var self = this,
            select = self.element,
            options = self.options,
            id = select.prop('id'),
            hiddenInputSelector = "#" + id,
            textInputId = "CombifyInput-" + id,
            textInputSelector = "#" + textInputId,
            name = select.prop('name'),
            selectedValue = select.find(':selected').val(),
            selectedText = select.find(':selected').text(),
            selectOptions = select.find('option'),
            optionArray = new Array();

            //Hide the original select
            select.hide();

            //Insert new HTML for a text input and a button to trigger the dropdown
            select.before('<div>' +
                '<span class="ui-combobox">' +
                '<input type="hidden" class="insertedInput" id="' + id + '" name="' + name + '" value="' + selectedValue + '">' +
                '<input type="text" id="' + textInputId + '" class="ui-combobox-input" value="' + selectedText + '">' +
                '</span></div>');

            //Remove the the id and name from the original select since they are now on the hidden input so that posted forms will get the correct value
            select.removeAttr('id', null)
            .removeAttr('name', null)
            .on('change', function (event) {
              event.stopPropagation();
          });

            //Get all the options from the select list and put them in an array for use in the autocomplete data source
            selectOptions.each(function (i) {
                optionArray.push($(this).text());
            });

            //Add autocomplete to the new input
            $(textInputSelector).autocomplete({
                source: optionArray,
                select: function (event, ui) {
                    //For some reason selecting a value doesn't automatically trigger the change event on the input, so trigger it here
                    this.value = ui.item.value;
                    var option = $(select).find('option').filter(function () { return $(this).html() == ui.item.value; }).first()
                    var selectValue = option.val();
                    
                    //set the value of the hidden input to the option value that matches the selected autocomplete value
                    $(hiddenInputSelector).val(selectValue);  
                    $(this).trigger('change');
                }
            })
            .on('change', function() {
                var value = $(this).val();
                var option = $(select).find('option').filter(function () { return $(this).html() == value; }).first()

								//If no matching option is found in the select list, then set the hidden input to the entered value
								if(!option.length){
                                  $(hiddenInputSelector).val(value);
                              }
                              else{
                                 $(hiddenInputSelector).val(option.val());
                             }
                         })
            .on('blur', function () {
                $(this).trigger('change');
            });

            //Convert entered values to upper case if capitalizeInput option is true
            // if (options.capitalizeInput) {
            //     var input = $(textInputSelector);
            //     input.css("text-transform", "uppercase").data('val', input.val()).on('keyup', function () {
            //         //Make the value upper case if it has changed
            //         var theInput = $(this);
            //         if (theInput.data('val') != this.value) {
            //             theInput.val(this.value.toUpperCase());
            //         }
            //         //Store the current value for comparison on next change
            //         theInput.data('val', this.value);

            //         theInput = null;
            //     });

            //     input = null;
            // }
            
            //If maximum length is required
            if (options.maxLength>0) {
				$(textInputSelector).keyup(function () {
					if($(this).val().length>options.maxLength) {
						var TempVal=$(this).val();
						$(this).data('val', TempVal.substring(0,options.maxLength));
						$(this).val(TempVal.substring(0,options.maxLength));
					}
				});
            }

            //Attach a change event to the select list to put the selected value in the new text input
            select.on('change', function () {
                var hiddenInput = $(this).prev().find("#" + id).first(); //hidden input
                var selectedValue = $(this).val();
                var text = $(this).find("option:selected").text();
                
                //find the option that matches the value
                var option = $(select).find('option').filter(function () { return $(this).html() == text }).first()
                hiddenInput.val(selectedValue);
                $(textInputSelector).val(option.text());  //set the visible textbox to the value of the options text
                hiddenInput.trigger('change');
            });

            //Add the button to trigger the dropdown
            // var button = select.prev().first().find(".ui-combobox-toggle");
            // button.button({
            //     icons: {
            //         primary: "ui-icon-triangle-1-s"
            //     },
            //     text: false
            // });

            //Attach the click event to the button to trigger the dropdown.
            // button.click(function (event) {
            //     event.stopPropagation();
            //     var minWidth = $(this).prev().first().width();
            //     ExpandSelectList($(this), event, minWidth);
            // });

            //Attach an event to expand the select list if the user presses Alt + DownArrow
            $(textInputSelector).keydown(function (event) {
                var list = $(this).parent().parent().next();

                if (event.which === 40 && event.altKey) {
                    //If the list is already visible then just hide it
                    event.preventDefault();
                    event.stopPropagation();
                    if (list.is(":visible")) {
                        list.hide();
                    }
                    else {
                        if (options.capitalizeInput) {
                            this.value = this.value.toUpperCase();
                        }
                        $(textInputSelector).autocomplete("close");
                        ExpandSelectList($(this), event, $(this).width());
                    }
                }
            });

            //Attach an event to close the list after a selection is made
            var list = select.prev().first().find(".ui-combobox-list");
            list.change(function () {
                $(this).hide();
            });

            //private methods
            function ExpandSelectList(element, event, minWidth) {
                var list = element.parent().parent().next();

                //If the list is already open or the autocomplete list is open then close the list.
                if (list.is(":visible") || $(textInputSelector).autocomplete("widget").is(":visible")) {
                    list.hide();
                }
                else {
                    //Set the length of the select list to either the number of items in the list or 30, whichever is smaller
                    var size;
                    if (optionArray.length <= 30) {
                       size = optionArray.length;
                   }
                   else {
                    size = 30;
                }
                
                var sizeAttr = size === 1 ? 2 : size;
                list.css({ "width": "auto",
                    "position": "absolute",
                    "z-index": "1"
                    }) //Puts the list on top of all other elements
                    	    .prop("size", sizeAttr) //changes the select list to a listbox so that it will "expand"
                    	    .show();
                    	    
                            if (minWidth > list.width()) {
                                list.css("width", minWidth);
                            }
                            
                            var listLineHeight = parseInt(list.find('option').first().css('font-size'),10);
                            list.css("height", listLineHeight * (size + 1));

                    //Attach a one-time event to the document to close the list if the user clicks anywhere else on the page.
                    $(document).one("click", function () {
                        list.hide();
                    });

                    function nextItem(event) {
                        var down = "down",
                        up = "up"

                        //If the user presses up arrow move to the previous item in the list
                        if (event.which === 38 && list.is(":visible")) {
                            move(up);
                            return;
                        }

                        //If user presess down arrow move to the next item in the list
                        if (event.which === 40 && list.is(":visible")) {
                            move(down);
                            return;
                        }

                        //if the user presses enter trigger the change event on the input to set it's value to the selected value
                        if (event.which === 13 && list.is(":visible")) {
                            list.trigger("change");
                            list.hide();
                            return;
                        }

                        if (list.is(":visible")) {
                            list.hide();
                        }

                        function move(direction) {
                            event.preventDefault();
                            var selected = list.find(":selected");
                            if (direction === down) {
                                var nextItem = selected.next();
                            }
                            if (direction === up) {
                                var nextItem = selected.prev();
                            }
                            selected.prop('selected', false);
                            nextItem.prop('selected', "selected");
                        }
                    }

                    //Attach an event to move through the list with the arrow keys
                    $(document).off("keydown.combifySelect")
                    .on("keydown.combifySelect", nextItem);
                }
            }
        },

        _destroy: function() {
            var select = this.element,
            inputObj = select.prev().find('.insertedInput'),
            id = "",
            name = "";
            id = inputObj.prop('id');
            name = inputObj.prop('name');

            select.attr({
                id: id,
                name: name,
            });
            select.off('change');
            select.prev().remove();
            select.show();
        }
    });
})(jQuery);