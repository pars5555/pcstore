Number.prototype.formatMoney = function(c, d, t){
    var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "",
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t)
    + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function mergeObjects() {
  var cur = null,
      prev = null;
  for (var i = l = arguments.length - 1; i >= 0; i--) {
    prev = cur;
    cur = (function(params) { 
      return function() {
        for (var param in params) { 
          this[param] = params[param];
        }
      };
    })(arguments[i]);
    
    if (i < l) {
      cur.prototype = new prev;
    } 
  }
  return new cur;
};

function objVarCount(obj) {
    var varCount = 0;
    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            varCount = varCount + 1;
    }
    return varCount;
};

Array.prototype.in_array = function(p_val) {
	for(var i = 0, l = this.length; i < l; i++) {
		if(this[i] == p_val) {
			return true;
		}
	}
	return false;
};

Array.prototype.AllValuesSame = function(){

    if(this.length > 0) {
        for(var i = 1; i < this.length; i++)
        {
            if(this[i] !== this[0])
                return false;
        }
    } 
    return true;
};