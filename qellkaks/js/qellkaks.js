Function.prototype.method = function(name, func) {
    this.prototype[name] = func;
}

Date.method("getRoundedMinutes", function() {
    var minutes;
    var remainder = this.getMinutes() % 5;
    var amount = parseInt(this.getMinutes() / 5);
            
    if (3 > remainder) {
        minutes = amount * 5;
    } else {
        minutes = amount * 5 + 5;
    }
    
    return (60 == minutes) ? 0 : minutes;
});

Date.method("getRoundedHours", function() {
    var minutes = this.getMinutes();
    var hours   = this.getHours();
    
    if (57 < minutes) {
      hours++;
    }

    return (24 == hours) ? 0 : hours;
});

var Qlock = function(date) {
    this.date = date;
};

Qlock.method("update", function(date) {
    this.date = (date instanceof Date) ? date : new Date;
});

Qlock.method("words", function() {
    var words = [];      
    var minutes = this.date.getRoundedMinutes();
    var hours   = this.date.getRoundedHours();
            
            
    words.push("kell");
    words.push("on");

    switch(minutes) {
    case 5:
        words.push("viis-1");
        words.push("minuti");
        words.push("t");
        words.push("labi");
        break;
    case 10:
        words.push("kumme-1");
        words.push("minuti");
        words.push("t");
        words.push("labi");
        break;
    case 15:
        words.push("veerand");
        hours++;
        break;
    case 20:
        words.push("kaks-1");
        words.push("kumme-1");
        words.push("nd");
        words.push("minuti");
        words.push("t");
        words.push("labi");
        break;
    case 25:
        words.push("kaks-1");
        words.push("kumme-1");
        words.push("nd");
        words.push("viis");
        words.push("minuti");
        words.push("t");
        words.push("labi");
        break;
    case 30:
        words.push("pool");
        break;
    case 35:
        words.push("kahe");
        words.push("kumne");
        words.push("viie");
        words.push("minuti");
        words.push("parast");
        break;
    case 40:
        words.push("kahe");
        words.push("kumne");
        words.push("minuti");
        words.push("parast");
        break;
    case 45:
        words.push("kolm-1");
        words.push("veerand");
        break;
    case 50:
        words.push("kumne");
        words.push("minuti");
        words.push("parast");
        break;
    case 55:
        words.push("viie");
        words.push("minuti");
        words.push("parast");
        break;
    }

    if (30 <= minutes) {
        hours++;
    }

    if (hours > 11) {
        hours = hours - 12;
    }
    
    switch(hours) {
    case 0:
        words.push("kaks-2");
        words.push("teist-2");
        break;
    case 1:
        words.push("uks");
        break;
    case 2:
        words.push("kaks-2");
        break;
    case 3:
        words.push("kolm-2");
        break;
    case 4:
        words.push("neli");
        break;
    case 5:
        words.push("viis-2");
        break;
    case 6:
        words.push("kuus");
        break;
    case 7:
        words.push("seitse");
        break;
    case 8:
        words.push("kaheksa");
        break;
    case 9:
        words.push("uheksa");
        break;
    case 10:
        words.push("kumme-2");
        break;
    case 11:
        words.push("uks");
        words.push("teist-1");
        break;
    }
                    
    return words;
});

$(function() {
    
    
    $(window).bind("resize", function() {
        if ($(window).height() > $(window).width()) {
            var new_size = parseInt($(window).width() / 20);
            $("#qlock > div ").css("font-size", new_size);
        } else {
            var new_size = parseInt($(window).height() / 20);
            $("#qlock > div ").css("font-size", new_size);            
        }
    }).trigger("resize");
    
  
    if (window.location.search.match(/test/)) {
        /* Test code to run one day in loop. */
        
        var now = new Date();
        now.setHours(0);
        now.setMinutes(0);

        var minutes = 0;
        var interval = setInterval(function() {
            var qlock = new Qlock(now);
            
            minutes++;
            var next_minute = new Date(now.getTime() + minutes * 60000);            
            qlock.update(next_minute);   
            // console.log(qlock.date.getRoundedHours() + ":" + qlock.date.getRoundedMinutes() + "-" + qlock.date.getHours() + ":" + qlock.date.getMinutes());
            $("#qlock > div > span").removeClass("active");
            $.each(qlock.words(), function(index, value) {
                $("#" + value).addClass("active");
            });
            if (1440 == minutes) {
                clearInterval(interval);
            }
        }, 500);
        
    } else {
        
        /* Normal code updated every second. */
        var interval = setInterval(function() {
            var now   = new Date();
            var qlock = new Qlock(now);
            
            $("#qlock > div > span").removeClass("active");
            
            $.each(qlock.words(), function(index, value) {
                $("#" + value).addClass("active");
            });

        }, 1000);         
    }

});
