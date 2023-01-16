/**
 * Base de reuso para applicações RHHealth
 * 
 */
var ITHEALTH_LIBRARY = function( strUrl ) {
    
    this.debugOnConsole      = (debug_console === true);

    this.consoleExists = function( ) {
        return (typeof console !== "undefined");
    };
}


ITHEALTH_LIBRARY.prototype.log = function( mixVar ) {
    if (this.consoleExists()===true && this.debugOnConsole===true){
        return console.log(mixVar);       
    }
};

var ITHEALTH = null;

ITHEALTH = new ITHEALTH_LIBRARY();

ITHEALTH.log('ITHEALTH_LIBRARY :: START');