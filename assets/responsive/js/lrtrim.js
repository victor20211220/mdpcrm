String.prototype.rtrim = function (s) {
    if (s == undefined)
        s = '\\s';
    return this.replace(new RegExp("[" + s + "]*$"), '');
};

String.prototype.ltrim = function (s) {
    if (s == undefined)
        s = '\\s';
    return this.replace(new RegExp("^[" + s + "]*"), '');
};
