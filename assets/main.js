// search
var substringMatcher = function(strs) {
    return function findMatches(q, cb) {
        var matches, substringRegex;
        matches = [];
        substrRegex = new RegExp(q, 'i');

        $.each(strs, function(i, str) {
            if (substrRegex.test(str)) {
                matches.push(str);
            }
        });

        cb(matches);
    };
};

function search() {
    $('#subjects').typeahead({
        hint: true,
        highlight: true,
        minLength: 0
    }, {
        name: 'subjects',
        source: substringMatcher(subjects)
    });
}
search();

function search2() {
    $('#subjects2').typeahead({
        hint: true,
        highlight: true,
        minLength: 0
    }, {
        name: 'subjects',
        source: substringMatcher(subjects)
    });
}
search2();

var place = "sut";
$(function() {
    $('#btn1').click(function(e) {
        $("#infoSut").delay(100).fadeIn(100);
        $("#infoOther").fadeOut(100);
        $('#btn2').removeClass('active');
        $(this).addClass('active');
        getMajor('1');
        place = "sut";
        $('#sut').prop('disabled', false);
        $('#other').prop('disabled', true);
        e.preventDefault();
    });
    $('#btn2').click(function(e) {
        $("#infoOther").delay(100).fadeIn(100);
        $("#infoSut").fadeOut(100);
        $('#btn1').removeClass('active');
        $(this).addClass('active');
        place = "other";
        $('#sut').prop('disabled', true);
        $('#other').prop('disabled', false);
        e.preventDefault();
    });
});


// form
var sut = document.getElementById('sut');
var other = document.getElementById('other');

var infoSut = document.getElementById('infoSut');
var infoOther = document.getElementById('infoOther');

// form grade type
var tscore = document.getElementById('tscore');
var myscore = document.getElementById('myscore');
var gtype = document.getElementById('gtype');
var point = document.getElementById('point');

if (tscore.checked) {
    point.innerHTML = "";
}

tscore.onclick = function() {
    point.innerHTML = "";
}

myscore.onclick = function() {
    getGtype(gtype.value);
}

if (myscore.checked) {
    getGtype(gtype.value);
}

function getGtype(gtype) {
    if (tscore.checked) {
        point.innerHTML = "";
        return;
    }
    var arr = gtype.split(", ");
    var len = arr.length;
    var text = '<table>';
    for (var i = 1; i < len - 1; i++) {
        var each = arr[i].split(":");
        var lebel = "<tr><td><lebel>เกรด " + each[1] + "</lebel></td>";
        var sign = "<td>>=</td>";
        var input = '<td><input onchange="checkPoint(\'' + arr[0] + '\')" id="' + each[0] + '" type="number" min="0" max="100" name="' + each[0] +
            '" value="' + each[2] + '"></td>';
        text += lebel + sign + input + "<td>คะแนน</td></tr>";
    }
    point.innerHTML = text + "</table><br>";
}

function checkPoint(type) {
    var one = document.getElementById('one');
    var two = document.getElementById('two');
    var three = document.getElementById('three');
    var four = document.getElementById('four');
    var five = document.getElementById('five');
    var six = document.getElementById('six');
    var seven = document.getElementById('seven');
    // one
    if (parseInt(one.value) > 100 || one.value == '' || parseInt(one.value) < 30) {
        one.value = 90;
    }
    // two
    if (parseInt(two.value) >= parseInt(one.value) || two.value == '' || parseInt(two.value) > 100 || parseInt(two.value) < 20) {
        two.value = parseInt(one.value) - 5;
    }
    // three
    if (parseInt(three.value) >= parseInt(two.value) || three.value == '' || parseInt(three.value) > 100 || parseInt(three.value) < 10) {
        three.value = parseInt(two.value) - 5;
    }
    // four
    if (parseInt(four.value) >= parseInt(three.value) || four.value == '' || parseInt(four.value) > 100 || parseInt(four.value) < 10) {
        four.value = parseInt(three.value) - 5;
    }
    if (type == 'eng8' || type == 'digit8' || type == 'eng6') {
        // five
        if (parseInt(five.value) >= parseInt(four.value) || five.value == '' || parseInt(five.value) > 100 || parseInt(five.value) < 5) {
            five.value = parseInt(four.value) - 5;
        }
    }
    if (type == 'eng8' || type == 'digit8') {
        // six
        if (parseInt(six.value) >= parseInt(five.value) || six.value == '' || parseInt(six.value) > 100 || parseInt(six.value) < 5) {
            six.value = parseInt(five.value) - 5;
        }
        // seven
        if (parseInt(seven.value) >= parseInt(six.value) || seven.value == '' || parseInt(seven.value) > 100 || parseInt(seven.value) < 5) {
            seven.value = parseInt(six.value) - 5;
        }
    }
}

function getMajor(m) {
    var ma = [];
    switch (m) {
        case '1':
            ma = sci;
            break;
        case '2':
            ma = soc;
            break;
        case '3':
            ma = farm;
            break;
        case '4':
            ma = eng;
            break;
        case '5':
            ma = doc;
            break;
        case '6':
            ma = nur;
            break;
        case '7':
            ma = den;
            break;
        default:
            ma = oth;
            break;
    }
    var txt = '<select name="school" onchange="checkSchool(this.value)">';
    for (var i in ma) {
        txt += '<option value="' + ma[i] + '">' + ma[i] + '</option>';
    }
    document.getElementById('school').innerHTML = txt + '</select>';
}
getMajor('1');

function checkSchool(v) {
    if (v == "Other") {
        document.getElementById('nschool').style.display = 'block';
        document.getElementById('school').value = document.getElementById('school2').value;
    } else {
        document.getElementById('nschool').style.display = 'none';
    }
}

function marking(id) {
    var total = 0;
    for (var i = 1; i < 6; i++) {
        var x = document.getElementById('mark' + i);
        if (x.value == '' || parseInt(x.value) < 0) x.value = '0';
        total += parseInt(x.value);
    }
    if (total > 100) {
        total -= parseInt(document.getElementById(id).value);
        document.getElementById(id).value = 0;
    }

    document.getElementById('total').value = total;
}

function validateForm() {
    var validate = true;
    var subId, subjects, instructor;
    var txtSubId, txtSubject, txtTeacher, txtFile, txtFill;
    if (place == "sut") {
        subId = document.getElementById('subId');
        subjects = document.getElementById('subjects');
        instructor = document.getElementById('instructor');

        txtSubId = "Course Code\n";
        txtSubject = "Course Title\n";
        txtTeacher = "Instructor \n";
        txtFile = "\nPlease upload Excel file (.xlsx)\n";
        txtFill = "Please Fill : \n";
    } else {
        subId = document.getElementById('subId2');
        subjects = document.getElementById('subjects2');
        instructor = document.getElementById('instructor2');

        txtSubId = "รหัสวิชา\n";
        txtSubject = "ชื่อวิชา\n";
        txtTeacher = "ชื่อผู้สอน\n";
        txtFile = "\nกรุณาอัพโหลดไฟล์ Excel (.xlsx)\n";
        txtFill = "กรุณากรอกข้อมูล : \n";
    }

    var txtAlert = '';
    if (subId.value == '') {
        validate = false;
        txtAlert += txtSubId;
    }

    if (subjects.value == '') {
        validate = false;
        txtAlert += txtSubject;
    }

    if (instructor.value == '') {
        validate = false;
        txtAlert += txtTeacher;
    }

    if (document.getElementById('total').value != 100 && place == "sut") {
        if (validate) txtFill = "";
        validate = false;
        txtAlert += "\nMarking Scheme Total not equal 100\n";
    }

    var myfile = document.getElementById('myfile');
    var t = myfile.value.split(".").pop().toLowerCase();
    if (myfile.files.length == 0 || (t != "xlsx" && t != "xls")) {
        validate = false;
        txtAlert += txtFile;
    }

    if (!validate) {
        alert(txtFill + txtAlert);
    }

    return validate;
    //return true;
}