/*
	Return current time in specified format
	The days and months are written in German but you can replace them :)
	Also plan to build in support for future/past date timestamps

	Arguments:
	pattern {String} 

	Pattern examples:

	time('$d.$m.$Y $h:$i Uhr') => 17.02.2012 13:30 Uhr
	time('$d/$m/$y | $h:$i Uhr') => 17/02/12 | 13:30 Uhr
	time('$D, $M $d, $Y @ $h:$i') => Freitag, Februar 17, 2012 @ 13:30
	time('Heute ist $D der $d $M. Das Jahr ist $Y.') => Heute ist Freitag der 17 Februar. Das Jahr ist 2012.



*/
function time(pattern){

    pattern = (pattern && typeof pattern === 'string') ? pattern : '';
    // if a pattern has been passed and we have at least something to change
    if (pattern !== '' && pattern.indexOf('$') !== -1) {
        var exp = /(\${1}[a-zA-Z]{1})/gi,
        dict = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
        d = new Date(),
        f = {
            // day number with 0
            '$d' : function(){
                return (d.getDate() < 10) ? '0'+  d.getDate() : d.getDate()
            },
            // day number
            '$j' : function(){
                return d.getDay()
            },
            // day name
            '$D' : function(){
                return dict[f.$j()];
            },
            // month name
            '$M' : function(){
                return dict[6+d.getMonth()+1];
            },
            // month number with 0
            '$m' : function(){
                return (d.getMonth() < 10) ? '0' + (d.getMonth()+1) : (d.getMonth()+1)
            },
            // hour in 24hr format
            '$h' : function(){
                return (d.getHours() < 10) ? '0'+ d.getHours() : d.getHours()
            },
            // minutes 
            '$i' : function(){
                return (d.getMinutes() < 10) ? '0' + d.getMinutes() : d.getMinutes()
            },
            // seconds
            '$s' : function(){
                return (d.getSeconds() < 10) ? '0' + d.getSeconds() : d.getSeconds()
            },
            // full year yyyy
            '$Y' : function(){
                return d.getFullYear()
            },
            // last two numbers of full year
            '$y' : function(){
                return d.getFullYear().toString().slice(2)
            }
        };
        var formatted = pattern.replace(exp, function (t) {            
            return f[t] ? f[t]() : '';
        });
        return formatted;
    }
    return pattern;
};    