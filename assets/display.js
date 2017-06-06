var xhttp;
var ihttp;
var shttp;
var data;
var numGrade = [];
var grade = [];
var gPercen = [];
var records = [];
var ctype = '';

var cnSort = 0;
var col = 2;
var numDisplay = 50;

var cookie = getCookie();
var json = "files/" + cookie + ".json";

var s = document.getElementById('showStatistics');
var g = document.getElementById('showChart');
var c = document.getElementById('showCal');
var d = document.getElementById('showData');

if (window.XMLHttpRequest) {
    // code for modern browsers
    xhttp = new XMLHttpRequest();
    ihttp = new XMLHttpRequest();
} else {
    // code for old IE browsers
    xhttp = new ActiveXObject("Microsoft.XMLHTTP");
    ihttp = new ActiveXObject("Microsoft.XMLHTTP");
}

xhttp.open("GET", json, true);
xhttp.send();
xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        data = JSON.parse(this.responseText);
        numGrade = data.numGrade;
        grade = data.grade;
        gPercen = data.gPercen;
        records = data.records;
        checkPlace()
        showStatistics();
        show(50, 1);
        showGradeChart();
        selNav('l0');
        showGradePercenChart();
    }

    if (this.status == 404) {
        document.getElementById('btnControl').innerHTML = '';
        document.cookie = "id=";
    }
}

var oid = 'l0';
var l0, l1, l2, l3, l4, l5;

function checkPlace() {
    if (data.place == "sut") {
        l0 = "Information";
        l1 = "Show Grade";
        l2 = "Show Calculate";
        l3 = "Show Graph";
        l4 = "Export Excel";
        l5 = "Score Distribution";
    } else {
        l0 = "ข้อมูลทั่วไป";
        l1 = "แสดงเกรด";
        l2 = "แสดงการคำนวณ";
        l3 = "แสดงกราฟ";
        l4 = "ดาวน์โหลดไฟล์";
    }
}

function getCookie() {
    var name = "id=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

// Chrome
window.onunload = function() {
    ihttp.open("POST", "delfile.php?name=" + cookie, true);
    ihttp.send();
}

// firefox
$(function() {

    try {
        opera.setOverrideHistoryNavigationMode('compatible');
        history.navigationMode = 'compatible';
    } catch (e) {}

    function ReturnMessage() {
        return "wait";
    }

    function del() {
        ihttp.open("POST", "delfile.php?name=" + cookie, true);
        ihttp.send();
    }

    function UnBindWindow() {
        $(window).unbind('beforeunload', ReturnMessage);
    }

    $(window).bind('beforeunload', del);
});

function selNav(id) {
    var sel = "";
    var t = '<ul>';

    if (id == 'l0') {
        sel = "class='active'";
        oid = id;
        showStatistics();
    }
    t += '<li id="l0" ' + sel + ' onclick="selNav(this.id)">' + l0 + '</li>';

    sel = '';
    if (id == 'l1') {
        sel = "class='active'";
        oid = id;
        showGrade();
    }
    t += '<li id="l1" ' + sel + ' onclick="selNav(this.id)">' + l1 + '</li>';

    if (data.ctype == 'tscore') {
        sel = '';
        if (id == 'l2') {
            sel = "class='active'";
            oid = id;
            showCal();
        }
        t += '<li id="l2" ' + sel + ' onclick="selNav(this.id)">' + l2 + '</li>';
    }

    sel = '';
    if (id == 'l3') {
        sel = "class='active'";
        oid = id;
        showChart();
    }
    t += '<li id="l3" ' + sel + ' onclick="selNav(this.id)">' + l3 + '</li>';

    sel = '';
    t += '<a href="download.php?col=' + col + '&cnSort=' + cnSort + '&id=' + cookie + '" target="_blank"><li id="l4" ' + sel + ' onclick="selNav(\"' + oid + '\")">' + l4 + '</li></a>';

    if (data.place == "sut") {
        t += '<a href="pdf.php?id=' + cookie + '" target="_blank"><li id="l5" ' + sel + ' onclick="selNav(\"' + oid + '\")">' + l5 + '</li></a>';
    }

    document.getElementById('nav').innerHTML = t + '</ul>';
}

function showStatistics() {
    var txt = '<b>';
    if (data.place == 'sut') {
        if (data.institutes == "Engineering")
            txt += '<img src="assets/logo.png" width="95" height="95"><br>';
        else
            txt += '<img src="assets/sut_logo.jpg" width="73" height="95"><br>';

        txt += "Instructor  " + data.instructor + "<br>";
        txt += "Institutes of " + data.institutes + "&nbsp;&nbsp;&nbsp;";
        txt += "School of " + data.school + "<br>";
        txt += "Course Code : " + data.subId + "<br>";
        txt += "Course Title : " + data.subjects + "<br>";
        txt += "Number of Credit : " + data.credit + "<br>";
        txt += "Term/Academic Year : " + data.term + "<br><br>";
    } else {
        txt += "ผู้สอน " + data.instructor + "<br>";
        txt += "วิชา " + data.subjects + "<br>";
        if (data.level != "")
            txt += "ระดับชั้น" + data.level + "<br>";
        txt += "ภาคการศึกษาที่  " + data.term + "<br><br>";
    }
    txt += '</b>';
    if (data.place == "sut") {
        txt += '<table width="60%" class="tb"><tr><th>Number of Student</th><th>Max. Score</th><th>Min. Score</th>\
                <th>Mean</th><th>SD</th><th>Class GPA</th></tr>';
    } else {
        txt += '<table width="60%" class="tb"><tr><th>จำนวนนักศึกษาทั้งหมด</th><th>คะแนนสูงสุด</th><th>คะแนนต่ำสุด</th>\
                <th>ค่าเฉลี่ย</th><th>ค่าเบี่ยงเบนมาตรฐาน</th><th>เกรดเฉลี่ยของวิชานี้</th></tr>';
    }
    txt += '<tr><td>' + data.student + '</td>';
    txt += '<td>' + data.max + '</td>';
    txt += '<td>' + data.min + '</td>';
    txt += '<td>' + data.mean + '</td>';
    txt += '<td>' + data.sd + '</td>';
    txt += '<td>' + data.classGPA + '</td></tr>';
    txt += '</table><br>';

    if (data.place == "sut") {
        txt += '<table width="30%" class="tb"><tr><th>Grade</th><th>Range</th>\
                    <th>No. of Student</th><th>%</th></tr>';
    } else {
        txt += '<table width="30%" class="tb"><tr><th>เกรด</th><th>ช่วงคะแนน</th>\
                    <th>จำนวนนักศึกษา</th><th>%</th></tr>';
    }
    for (var i in data.grade) {
        var k = data.range[i];
        if (k === undefined || k == "0-101")
            k = "-";

        txt += '<tr><td>' + data.grade[i] + '</td>';
        txt += '<td>' + k + '</td>';
        txt += '<td>' + data.numGrade[i] + '</td>';
        txt += '<td>' + data.gPercen[i] + '</td></tr>';
    }
    txt += data.place == "sut" ? '<tr><td>Total</td>' : '<tr style="background-color: #f3f3f3;"><td style="padding-top: 6px;">รวม</td>';
    txt += '<td>' + data.min + "-" + data.max + '</td><td>' + data.student + '</td><td>100</td></tr>';
    document.getElementById('showStatistics').innerHTML = txt + "</table><br>";
    showNormal();
}

function showNormal() {
    s.style.display = 'block';
    c.style.display = 'none';
    g.style.display = 'none';
    d.style.display = 'none';
}

function showGrade() {
    d.style.display = 'block';
    c.style.display = 'none';
    g.style.display = 'none';
    s.style.display = 'none';
    var label;
    var txt = '<select onchange="show(this.value, 1)" id="numshow" name="numshow">\
                <option value="50">50</option>\
                <option value="100">100</option>\
                <option value="200">200</option>\
                <option value="500">500</option>';
    if (data.place == "sut") {
        label = '<label>Show </label>';
        txt += '<option value="all">All</option></select> list';
    } else {
        label = '<label>แสดง</label> ';
        txt += '<option value="all">ทั้ง</option></select> รายการ';
    }

    $('#list').html(label + txt);
}

function showCal() {
    var txt = '',
        cols;
    if (data.place == "sut") {
        cols = ["No.", "Score", "Frequency", "Commulative Frequency", "cf-0.5f", "Percentile", "T-score"];
    } else {
        cols = ["ลำดับ", "คะแนน", "ความถี่", "ความถี่สะสม", "cf-0.5f", "เปอร์เซ็นต์ไทล์", "T-score"];
    }
    txt = '	<table width="50%" class="tb">\
                <tr><th>' + cols[0] + '</th><th>' + cols[1] + '</th><th>' + cols[2] + '</th><th>' + cols[3] + '</th>\
                    <th>' + cols[4] + '</th><th>' + cols[5] + '</th><th>' + cols[6] + '</th></tr>';

    for (var i in data.showCal) {
        txt += '<tr><td>' + (parseInt(i) + 1) + '</td>';
        txt += '<td>' + data.showCal[i].scoreFreq + '</td>';
        txt += '<td>' + data.showCal[i].freq + '</td>';
        txt += '<td>' + data.showCal[i].cumFreq + '</td>';
        txt += '<td>' + data.showCal[i].cf5f + '</td>';
        txt += '<td>' + data.showCal[i].percentile + '</td>';
        txt += '<td>' + data.showCal[i].tscore + '</td></tr>';
    }
    document.getElementById('showCal').innerHTML = txt + "</table>";

    c.style.display = 'block';
    s.style.display = 'none';
    g.style.display = 'none';
    d.style.display = 'none';
}

function showChart() {
    g.style.display = 'block';
    c.style.display = 'none';
    s.style.display = 'none';
    d.style.display = 'none';

    var gn, gp;
    if (data.place == "sut") {
        gn = 'Number of Student';
        gp = 'Percentage';
    } else {
        gn = 'จำนวนคนที่ได้เกรดต่างๆ';
        gp = 'คิดเป็นร้อยละ';
    }
    $('#gn').html(gn);
    $('#gp').html(gp);
}

function showGradeChart() {
    var ctx = document.getElementById("myGradeChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: grade, //["A", "B+", "B", "C+", "C", "D+", "D", "F"],
            datasets: [{
                label: '',
                data: numGrade,
                backgroundColor: data.color
            }]
        }
    });
}

function showGradePercenChart() {
    var ctx = document.getElementById("myGradePercenChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: grade,
            datasets: [{
                backgroundColor: data.color,
                data: gPercen
            }]
        }
    });
}

function show(num, page) {
    numDisplay = num;
    if (num == 'all') {
        num = records.length;
        numDisplay = num;
    }

    var n = records.length;
    var numPage = Math.ceil(n / parseFloat(num));
    var start = (page - 1) * num;
    var end = page * num;
    if (end > n) {
        end = n;
    }
    showMyScore(start, end);
    var selPage = '<br>';
    if (num != records.length) {
        var sel = "";
        if (page == 1) {
            sel = "selPage";
        } else if (numPage > 5) {
            selPage += '<div class="c-paging__prev-link" style="display: inline;" onclick="show(' + num + ',' + (page - 1) + ')"></div>';
        }
        selPage += '<div class="box ' + sel + '" style="display: inline;" onclick="show(' + num + ', 1)">1</div>&nbsp;';

        if (page < 5) {
            if (numPage < 5) {
                for (var i = 2; i <= numPage; i++) {
                    sel = "";
                    if (i == page) {
                        sel = "selPage";
                    }
                    selPage += '<div class="box ' + sel + '" style="display: inline;" onclick="show(' + num + ',' + i + ')">' + i + '</div>&nbsp;';
                }
            } else {
                var txt = '<div style="display: inline;">...</div>&nbsp;';
                selPage += createNumPage(2, 6, txt, page, num, numPage);
            }
        } else if (page >= numPage - 2) {
            var txt = '<div style="display: inline;">...</div>&nbsp;';
            selPage += txt;
            selPage += createNumPage(page - 1, numPage, "", page, num, numPage);
        } else {
            var txt = '<div style="display: inline;">...</div>&nbsp;';
            selPage += txt;
            selPage += createNumPage(page - 1, page + 2, txt, page, num, numPage);
        }
        document.getElementById('br').innerHTML = "<br><br>";
        document.getElementById('br2').innerHTML = "<br><br>";
    } else {
        document.getElementById('br').innerHTML = "";
        document.getElementById('br2').innerHTML = "";
    }
    document.getElementById('selPage').innerHTML = selPage;
    document.getElementById('selPage2').innerHTML = selPage;
}

function createNumPage(start, end, txt, page, num, numPage) {
    var selPage = "";
    var sel = "";
    for (var i = start; i < end; i++) {
        sel = "";
        if (i == page) {
            sel = "selPage";
        }
        selPage += '<div class="box ' + sel + '" style="display: inline;" onclick="show(' + num + ',' + i + ')">' + i + '</div>&nbsp;';
    }
    selPage += txt;
    sel = "";
    if (page == numPage) {
        sel = "selPage";
        selPage += '<div class="box ' + sel + '" style="display: inline;" onclick="show(' + num + ',' + numPage + ')">' + numPage + '</div>&nbsp;';
    } else {
        selPage += '<div class="box ' + sel + '" style="display: inline;" onclick="show(' + num + ',' + numPage + ')">' + numPage + '</div>&nbsp;';
        selPage += '<div class="c-paging__next-link" style="display: inline;" onclick="show(' + num + ',' + (page + 1) + ')"></div>';
    }

    return selPage;
}

function showMyScore(start, end) {
    var cols;
    if (data.place == "sut") {
        cols = ["No.", "ID", "Name", "Score", "Grade"];
    } else {
        cols = ["ลำดับ", "รหัส", "ชื่อ-สกุล", "คะแนน", "เกรด"];
    }
    var txt = '	<table width="45%" id="tbdata" class="tb">\
					<tr>\
						<th>' + cols[0] + '</th>\
						<th onclick="sortTable(0)" class="tdsort">' + cols[1] + '</th>\
						<th>' + cols[2] + '</th>\
						<th onclick="sortTable(2)" class="tdsort">' + cols[3] + '</th>\
						<th onclick="sortTable(3)" class="tdsort">' + cols[4] + '</th>\
					</tr>';

    for (var x = start; x < end; x++) {
        txt += '<tr><td>' + (parseInt(x) + 1) + '</td>';
        txt += '<td>' + records[x].id + '</td>';
        txt += '<td style="text-align: left;">' + records[x].name + '</td>';
        txt += '<td>' + records[x].score + '</td>';
        txt += '<td>' + records[x].grade + '</td></tr>';
    }
    document.getElementById('show').innerHTML = txt + "</table>";
}

function sortTable(col) {
    if (cnSort % 2 == 0) {
        if (col == 0) qSortAscId(0, data.student - 1);
        if (col == 1) qSortAscName(0, data.student - 1);
        if (col == 2) qSortAscScore(0, data.student - 1);
        if (col == 3) qSortAscGrade(0, data.student - 1);
    } else {
        if (col == 0) qSortDescId(0, data.student - 1);
        if (col == 1) qSortDescName(0, data.student - 1);
        if (col == 2) qSortDescScore(0, data.student - 1);
        if (col == 3) qSortDescGrade(0, data.student - 1);
    }
    show(numDisplay, 1);
    cnSort++;
    this.col = col;
    selNav(oid);
}

function qSortAscId(left, right) {
    if (left >= right) return;
    var pivot = left;
    for (var i = left; i <= right; i++) {
        if (records[i].id > records[left].id)
            swap(++pivot, i);
    }
    swap(pivot, left);
    qSortAscId(left, pivot - 1);
    qSortAscId(pivot + 1, right);
}

function qSortDescId(left, right) {
    if (left >= right) return;
    var pivot = left;
    for (var i = left; i <= right; i++) {
        if (records[i].id < records[left].id)
            swap(++pivot, i);
    }
    swap(pivot, left);
    qSortDescId(left, pivot - 1);
    qSortDescId(pivot + 1, right);
}

function qSortAscName(left, right) {
    if (left >= right) return;
    var pivot = left;
    for (var i = left; i <= right; i++) {
        if (records[i].name > records[left].name)
            swap(++pivot, i);
    }
    swap(pivot, left);
    qSortAscName(left, pivot - 1);
    qSortAscName(pivot + 1, right);
}

function qSortDescName(left, right) {
    if (left >= right) return;
    var pivot = left;
    for (var i = left; i <= right; i++) {
        if (records[i].name < records[left].name)
            swap(++pivot, i);
    }
    swap(pivot, left);
    qSortDescName(left, pivot - 1);
    qSortDescName(pivot + 1, right);
}

function qSortAscScore(left, right) {
    if (left >= right) return;
    var pivot = left;
    for (var i = left; i <= right; i++) {
        if (records[i].score > records[left].score)
            swap(++pivot, i);
    }
    swap(pivot, left);
    qSortAscScore(left, pivot - 1);
    qSortAscScore(pivot + 1, right);
}

function qSortDescScore(left, right) {
    if (left >= right) return;
    var pivot = left;
    for (var i = left; i <= right; i++) {
        if (records[i].score < records[left].score)
            swap(++pivot, i);
    }
    swap(pivot, left);
    qSortDescScore(left, pivot - 1);
    qSortDescScore(pivot + 1, right);
}

function qSortAscGrade(left, right) {
    if (left >= right) return;
    var pivot = left;
    for (var i = left; i <= right; i++) {
        if (records[i].grade > records[left].grade)
            swap(++pivot, i);
    }
    swap(pivot, left);
    qSortAscGrade(left, pivot - 1);
    qSortAscGrade(pivot + 1, right);
}

function qSortDescGrade(left, right) {
    if (left >= right) return;
    var pivot = left;
    for (var i = left; i <= right; i++) {
        if (records[i].grade < records[left].grade)
            swap(++pivot, i);
    }
    swap(pivot, left);
    qSortDescGrade(left, pivot - 1);
    qSortDescGrade(pivot + 1, right);
}

function swap(x, y) {
    var tId = records[x].id;
    records[x].id = records[y].id;
    records[y].id = tId;

    var tName = records[x].name;
    records[x].name = records[y].name;
    records[y].name = tName;

    var tScore = records[x].score;
    records[x].score = records[y].score;
    records[y].score = tScore;

    var tGrade = records[x].grade;
    records[x].grade = records[y].grade;
    records[y].grade = tGrade;
}