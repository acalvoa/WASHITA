// Process items count
var WASHING_TYPE = "washing";
var IRONING_TYPE = "ironing";
var DRYCLEANING_TYPE = "drycleaning";
var SPECIALCLEANING_TYPE = "drycleaning";

function WashItem(type, id, name, weight /* kilos */, dryCleaningPrice, specialCleaningPrice, imageUri){
    this.Type = type;
    this.Id = id;
    this.Name = name;
    this.Weight = weight;
    this.DryCleaningPrice = dryCleaningPrice;
    this.SpecialCleaningPrice = specialCleaningPrice;
    this.ImageUri = imageUri;
}

function WashItemLine(count, washItem){
    this.count = count;
    this.item = washItem;
}

WashItemLine.prototype.asText = function(){
        return this.item.Name + ' x'+this.count;
}
WashItemLine.prototype.getWeight = function(){
        return this.item.Weight * this.count;
}

WashItemLine.prototype.getDryCleaningPrice = function(){
        return this.item.DryCleaningPrice * this.count;
}
WashItemLine.prototype.getSpecialCleaningPrice = function(){
        return this.item.SpecialCleaningPrice * this.count;
}
WashItemLine.prototype.setCount = function(count){
        this.count = count;
}

function WashProduct(washItemLines){
    this.itemLines = washItemLines;
}
WashProduct.prototype.getWeight = function(type){
    var weight = 0;
    $.each(this.itemLines, function( key, value ) {
        if(value.Type === type){
            weight += value.getWeight();
        }
    });
         
    if(weight < 1){
        weight = 1; // set minimum;
    }

    return weight.toFixed(2);
}
WashProduct.prototype.getSanitizedWeight = function(type){
    var weight = this.getWeight(type);
    return getSanitizedAndRoundedUpNumber(weight.toString(),2);
}


WashProduct.prototype.getDryCleaningPrice = function(){
    var price = 0;
    $.each(this.itemLines, function( key, value ) {
        if(value.Type === DRYCLEANING_TYPE){
            price += value.getDryCleaningPrice();
        }
    });

    return price;
}
WashProduct.prototype.getSpecialCleaningPrice = function(){
    var price = 0;
    $.each(this.itemLines, function( key, value ) {
        if(value.Type === SPECIALCLEANING_TYPE){
            price += value.getSpecialCleaningPrice();
        }
    });

    return price;
}

WashProduct.prototype.findWashItemLine = function(washItemId){
    var result = null;
    $.each(this.itemLines, function( key, value ) {
        if(value.item.Id == washItemId){
            result =  value;
        }
    });
         
    return result;
}


function WashItemsControl(type,modalWindowName){
    var self = this;
    this.type = type;
    this.modalWindowName = modalWindowName;
    this.washItemsCached = null;
    this.getWashItems = function (callback) {
        if(self.washItemsCached == null){
            $.ajax({
                url: "process_washitems.php",
                dataType: 'json', 
            }).done(function(data){
                //console.log(data);
                self.washItemsCached = [];
                
                var res = $.parseJSON(data);
                $.each(res, function( key, value ) {
                    self.washItemsCached.push(new WashItem(self.type, value.Id, value.Name, value.Weight, value.IroningPrice, value.DryCleanPrice, value.SpecialCleanPrice, value.ImageUri));
                });
                callback(self.washItemsCached);
            });
        }
        else{
            callback(self.washItemsCached);
        }
    };
    this.getDefaultWashItemLines = function (callback) {
         var callbackLocal = function(washItems){
            var result = [];
            $.each( washItems, function( key, value ) {
                result.push(new WashItemLine(0, value));
            });
            callback(result);
         };
        var washItems = self.getWashItems(callbackLocal);
    };
    this.createInputItem = function (washItemLine) {
       return '<div class="input-group washitem-line">'+
                '<img src="'+washItemLine.item.ImageUri+'" class="washitem">'+
                '<input class="form-control numbersOnly items" data-washitemid="'+washItemLine.item.Id+'" type="number" min="0" max="1000" step="1.0" value="'+washItemLine.count+'"'+
                ' lang="es" />'+
                '<span class="order-kg">'+washItemLine.item.Name+'</span>'+
              '</div>'; 
    };
    this.createSelectedItemLine = function(washItemLine){
        return '<div class="order-line">'+
                    '<input type="checkbox" style="display:none" name="washitems[]" value="'+washItemLine.item.Id+','+washItemLine.count+'" checked>'+
                    '<span class="order-line-label">'+washItemLine.item.Name+'</span><span class="order-line-value">x'+washItemLine.count+'</span>'+
                '</div>';
  
    };
    this.SetWashItems = function(washItemLineKeys, callback){
        callback = typeof callback !== 'undefined' ? callback : null;

        var callbackLocal = function(washItemLines){
            self.washProduct = new WashProduct(washItemLines);
            $.each(self.washProduct.itemLines, function( key, value ) {
                var count = washItemLineKeys[value.item.Id];
                if(count !== 'undefined' && count > 0){
                    value.setCount(count);
                }
                else{
                    value.setCount(0);
                }
            }); 

            if(callback != null){
                callback();
            }
        };

        self.getDefaultWashItemLines(callbackLocal);
    };
    this.washProduct = null;
    this.openModalWindow = function(){
        var placeHolder = $(self.modalWindowName+" .modal-possible-items-placeholder");
        placeHolder.html('');
        //populate placeholder in modal window 
        $.each(self.washProduct.itemLines, function( key, value ) {
            var inputItem = $.parseHTML(self.createInputItem(value));
            placeHolder.append(inputItem);
            $(inputItem).find('input').bind("change paste keyup", function() {
                var $input = $(this);
                var itemLine = self.washProduct.findWashItemLine($input.attr('data-washitemid'));
                itemLine.setCount(parseInt($input.val()));
                self.onWashItemAmountChanged();
            });
         });
    };
    this.GetHtmlForChosenItems = function(){
         //show selected items
        var html = '';
        if(self.washProduct != null){
            $.each(self.washProduct.itemLines, function( key, value ) {
                if(value.count > 0){
                    html += self.createSelectedItemLine(value); 
                }
            });
        }

        return html;
    }
    this.closeModalWindow = function(){        
        if(this.OnWashItemChoosed != null){
            this.OnWashItemChoosed();
        }
    };
    this.OnWashItemChoosed = null;
    this.HasAnyItem = function(){
        if(this.washProduct == null || this.washProduct.itemLines.length < 1){
            return false;
        } 
        
        var result = false;
        this.washProduct.itemLines.forEach(function(item, index) {
            if(item.count > 0){
                result = true;
            }   
        });

        return result;
    };
}

WashItemsControl.prototype.onWashItemAmountChanged = function(){
};




// Inherit from WashItemsControl
function WashingWashItemsControl(modalName, preload) {
    WashItemsControl.call(this, WASHING_TYPE, modalName, preload);

    var self = this;

    //preload wash items
    if(preload){
        self.getWashItems(function(w){});
    }

    var emptyWashItems = {};
    self.SetWashItems(emptyWashItems);

    var modal = $(modalName);
    modal.on('shown.bs.modal', function() {
        //if first time
        if(self.washProduct == null){
            self.SetWashItems(emptyWashItems);
            self.openModalWindow();
        }
        else{
            self.openModalWindow();
        }
    })

    modal.on('hidden.bs.modal', function() {
        self.closeModalWindow();
    })
}
WashingWashItemsControl.prototype = Object.create(WashItemsControl.prototype); // See note below
// Set the "constructor" property to refer to WashItemsControl
WashingWashItemsControl.prototype.constructor = WashingWashItemsControl;
WashingWashItemsControl.prototype.onWashItemAmountChanged = function(){
    $("#modal_possible_items_weight").html(this.washProduct.getSanitizedWeight(WASHING_TYPE));
};


function IroningWashItemsControl(ironingPlaceHolderName, ironingCheckboxName) {
    var self = this;
    this.ironingPlaceHolderName =ironingPlaceHolderName;
    this.washProduct = new WashProduct([]);

    var ironingCheckbox = $(ironingCheckboxName);
    ironingCheckbox.change(function() {
        if($(this).is(":checked")){
            self.enable();
        }
        else{
            self.disable();
        }
    });

    var addButton = $(ironingPlaceHolderName+" .ironing-add-more-item");
    addButton.click(function(e){
        self.addNewItem();
    });

    this._isEnabled = false;
    this.enable = function(){
        if(self._isEnabled){
            return;
        }
        self._isEnabled = true;

        if(this.totalItems() == 0){
            this.addNewItem();
        }

        self._raiseOnWashItemAmountChanged();
        $(self.ironingPlaceHolderName).show();        
    };

    this.disable = function(){
        if(!self._isEnabled){
            return;
        }
        self._isEnabled = false;
        self._raiseOnWashItemAmountChanged();
        $(self.ironingPlaceHolderName).hide();        
    };

    this._raiseOnWashItemAmountChanged = function(){
        if(self.onWashItemAmountChanged != undefined){
            self.onWashItemAmountChanged();
        }
    }
    this.addNewItem = function(){
        var newWashItem = new WashItem(IRONING_TYPE, 0, "", 0, 0, 0,0,"");
        var newWashItemLine = new WashItemLine(1, newWashItem);
        self.washProduct.itemLines.push(newWashItemLine);
        newWashItemLine.item.Id = self.washProduct.itemLines.indexOf(newWashItemLine);

        self.addItemToHtml(newWashItemLine);

        self._raiseOnWashItemAmountChanged();
    }

    this.addExistingItem = function(ironingItemLine){
        self.washProduct.itemLines.push(ironingItemLine);
        self.addItemToHtml(ironingItemLine);
        self._raiseOnWashItemAmountChanged();
    }
    
    this.removeItem = function(id){
        var item = self.washProduct.findWashItemLine(id);
        var itemIndex = self.washProduct.itemLines.indexOf(item);
        self.washProduct.itemLines.splice(itemIndex,1);

        self.removeItemFromHtml(id);
        
        self._raiseOnWashItemAmountChanged();
    }
    
    this.totalItems = function(){
        var totalItems = 0;
        if(this._isEnabled){
            $.each(self.washProduct.itemLines, function( key, value ) {
                totalItems += value.count;
            });
        }

        return totalItems;
    }

    this.createInputItem = function (washItemLine) {
       return '<div class="input-group washitem-line ironing-items" data-washitemid="'+washItemLine.item.Id+'">'+
                '<input class="form-control numbersOnly items ironing-items-number" data-washitemid="'+washItemLine.item.Id+'"'+
                ' type="number" min="1" max="1000" step="1.0" value="'+washItemLine.count+'"'+
                ' lang="es" name="ironing_item_number" />'+
                '<input class="form-control textinput" maxlength="60"'+
                    ' name="ironing_item_name" data-washitemid="'+washItemLine.item.Id+'"' +
                    ' value="'+washItemLine.item.Name+'"></input>'+
                '<span  class="fa fa-minus-square-o remove-ironing-item" data-washitemid="'+washItemLine.item.Id+'"></span>'+
              '</div>'; 
    };
    this.addItemToHtml = function(washItemLine){
        var placeHolder = $(self.ironingPlaceHolderName+" .ironing_placeholder_items");
        var inputItem = $.parseHTML(self.createInputItem(washItemLine));
        placeHolder.append(inputItem);
        $(inputItem).find('input.numbersOnly').bind("change paste keyup", function() {
                var $input = $(this);
                var itemLine = self.washProduct.findWashItemLine($input.attr('data-washitemid'));
                itemLine.setCount(parseInt($input.val()));

                self._raiseOnWashItemAmountChanged();
            });
        $(inputItem).find('input.textinput').bind("change paste keyup", function() {
                var $input = $(this);
                var itemLine = self.washProduct.findWashItemLine($input.attr('data-washitemid'));
                itemLine.item.Name = $input.val();
            });
        $(inputItem).find('.remove-ironing-item').click(function(e) {
                e.preventDefault();
                self.removeItem($(this).attr('data-washitemid'));
            });
    };
    this.removeItemFromHtml = function(id){
        var placeHolder = $(self.ironingPlaceHolderName);
        var itemLine =placeHolder.find('div.ironing-items[data-washitemid="'+id+'"]');
        itemLine.remove();
    };

    this.setItemsLines = function(ironingItemLines){
        $.each(ironingItemLines, function( key, line ) {
            self.addExistingItem(line);
        });
    }
}


// Inherit from WashItemsControl
function DryCleainigWashItemsControl(modalName, preload) {
    WashItemsControl.call(this, DRYCLEANING_TYPE, modalName, preload);

    var self = this;

    //preload wash items
    if(preload){
        self.getWashItems(function(w){});
    }

    //if first time
    if(self.washProduct == null){
        var emptyWashItems={};
        self.SetWashItems(emptyWashItems, function(){
                self.showInputItems();
            });
        
    }

    this.showInputItems = function(){
        var placeHolder = $(self.modalWindowName);
        placeHolder.html('');
        //populate placeholder in modal window 

        var itemsInLefColumn = Math.ceil(self.washProduct.itemLines.length/2);
        var leftColumn = $.parseHTML('<div class="dry-cleaning-column"></div>');
        var rightColumn = $.parseHTML('<div class="dry-cleaning-column"></div>');
        placeHolder.append(leftColumn);
        placeHolder.append(rightColumn);

        $.each(self.washProduct.itemLines, function( key, value ) {
            var inputItem = $.parseHTML(self.createInputItem(value));
            column = ((key+1) <=itemsInLefColumn)? $(leftColumn): $(rightColumn);
            column.append(inputItem);
            $(inputItem).find('input').bind("change paste keyup", function() {
                var $input = $(this);
                var itemLine = self.washProduct.findWashItemLine($input.attr('data-washitemid'));
                itemLine.setCount(parseInt($input.val()));
                self.onWashItemAmountChanged();
            });
         });
    };

    this.createInputItem = function (washItemLine) {
       return '<div class="input-group washitem-line">'+
                '<input class="form-control numbersOnly items" data-washitemid="'+washItemLine.item.Id+'" type="number" min="0" max="1000" step="1.0" value="'+washItemLine.count+'"'+
                ' lang="es" />'+
                '<span class="order-kg">'+washItemLine.item.Name+'</span>'+
              '</div>'; 
    };
}


DryCleainigWashItemsControl.prototype = Object.create(WashItemsControl.prototype); // See note below
// Set the "constructor" property to refer to WashItemsControl
DryCleainigWashItemsControl.prototype.constructor = DryCleainigWashItemsControl;
DryCleainigWashItemsControl.prototype.onWashItemAmountChanged = function(){
    $("#modal_possible_items_dry_cleaning_price").html("$"+JsFloatToChileanNumber(this.washProduct.getDryCleaningPrice()));
};