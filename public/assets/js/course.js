// public/assets/js/course.js
(function () {
    "use strict";

    if (typeof window.jQuery === "undefined") {
        console.error("course.js: jQuery not found. Load jQuery BEFORE this script.");
        return;
    }
    var $ = window.jQuery;

    $(function () {
        var cfg = window.CourseFormConfig || {};
        cfg.studentsUrl = cfg.studentsUrl || "/sistem-akademik/get-students-by-jurusan";
        cfg.recommendationsUrl = cfg.recommendationsUrl || "/sistem-akademik/course/get-recommendations";
        cfg.conflictUrl = cfg.conflictUrl || "/sistem-akademik/course/check-conflicts";
        cfg.initialKelas = cfg.initialKelas || null;
        cfg.preselectSiswa = Array.isArray(cfg.preselectSiswa) ? cfg.preselectSiswa : [];
        cfg.initialHari = cfg.initialHari || "";
        cfg.currentCourseId = cfg.currentCourseId || null;

        var slotIds = (cfg.slotIds || []).map(String);
        var slotDetails = cfg.slotDetails || {};

        function slotIndex(id) {
            return slotIds.indexOf(String(id));
        }

        var $selectSiswa = $("#siswa_ids");
        var $slotStart = $("#slot_start");
        var $slotEnd = $("#slot_end");
        var availableSlotsCache = null;

        // Init Select2
        if ($.fn && $.fn.select2) {
            $selectSiswa.select2({
                placeholder: "Pilih siswa...",
                width: "100%",
            });
        }

        // --- STUDENT FILTERING ---
        function setLoading(on) {
            if (on) $("#students-loading").removeClass("d-none");
            else $("#students-loading").addClass("d-none");
        }

        function loadStudentsByKelas(kelasId, preselectIds) {
            if (!kelasId) {
                $selectSiswa.find("option").remove();
                $selectSiswa.val(null).trigger("change");
                return;
            }

            setLoading(true);
            $selectSiswa.prop("disabled", true);

            $.ajax({
                url: cfg.studentsUrl,
                type: "GET",
                data: { kelas_id: kelasId },
                dataType: "json",
            }).done(function (res) {
                setLoading(false);
                $selectSiswa.prop("disabled", false);
                $selectSiswa.find("option").remove();

                if (res && res.success && Array.isArray(res.students)) {
                    res.students.forEach(function (s) {
                        var nama = (s.user && s.user.nama) ? s.user.nama : (s.nama || "Siswa " + s.id);
                        var nis = s.nis || s.nisn || "-";
                        var label = nama + " - " + nis;
                        var opt = new Option(label, s.id, false, false);
                        $selectSiswa.append(opt);
                    });

                    if (Array.isArray(preselectIds) && preselectIds.length > 0) {
                        $selectSiswa.val(preselectIds);
                    } else if (res.select_all) {
                        var allVals = res.students.map(function(s){ return s.id; });
                        $selectSiswa.val(allVals);
                    }
                }
                $selectSiswa.trigger("change");
            }).fail(function () {
                setLoading(false);
                $selectSiswa.prop("disabled", false);
            });
        }

        $("#kelas_id").on("change", function () {
            var kelasId = $(this).val();
            loadStudentsByKelas(kelasId, []);
            applyRuanganFallback(kelasId);
            filterMapelByKelasJurusan();
            if ($("#hari").val()) fetchRecommendations();
        });

        function filterMapelByKelasJurusan() {
            var selectedKelasOpt = $("#kelas_id").find("option:selected");
            var kelasJurusan = selectedKelasOpt.data("jurusan") || "";
            kelasJurusan = kelasJurusan.toString().toLowerCase().trim();

            var $mapelSelects = $("#mata_pelajaran_umum, #mata_pelajaran_jurusan");
            var $hiddenMapel = $("#mata_pelajaran_id");
            var currentVal = $hiddenMapel.val();
            var validValFound = false;
            
            // Allow initial mapel
            var initialMapel = cfg.initialMapelId || currentVal;

            $mapelSelects.find("option").each(function () {
                var $opt = $(this);
                if (!$opt.val()) return; // skip placeholder

                var mapelJurusan = ($opt.data("jurusan") || "").toString().toLowerCase().trim();
                
                var show = (mapelJurusan === "umum" || mapelJurusan === "" || !kelasJurusan || mapelJurusan === kelasJurusan || $opt.val() === currentVal || $opt.val() === initialMapel);
                
                if (show) {
                    $opt.prop("disabled", false);
                    if ($opt.parent().is("span.hide-opt")) {
                        $opt.unwrap();
                    }
                    if ($opt.val() === currentVal) validValFound = true;
                } else {
                    $opt.prop("disabled", true);
                    if (!$opt.parent().is("span.hide-opt")) {
                        $opt.wrap('<span class="hide-opt" style="display:none;"></span>');
                    }
                }
            });

            if ($.fn.select2) {
                $("#mata_pelajaran_umum").select2("destroy").select2({ width: "100%", allowClear: true });
                $("#mata_pelajaran_jurusan").select2("destroy").select2({ width: "100%", allowClear: true });
            }

            if (currentVal && !validValFound) {
                $("#mata_pelajaran_umum").val("").trigger("change.select2");
                $("#mata_pelajaran_jurusan").val("").trigger("change.select2");
                $hiddenMapel.val("").trigger("change");
            }
        }

        if (cfg.initialKelas) {
            loadStudentsByKelas(cfg.initialKelas, cfg.preselectSiswa);
        }

        // Handle Mapel Dual Selectors
        $("#mata_pelajaran_umum").on("change", function () {
            if ($(this).val()) {
                $("#mata_pelajaran_jurusan").val("").trigger("change.select2");
                $("#mata_pelajaran_id").val($(this).val()).trigger("change");
            } else if (!$("#mata_pelajaran_jurusan").val()) {
                $("#mata_pelajaran_id").val("").trigger("change");
            }
        });
        $("#mata_pelajaran_jurusan").on("change", function () {
            if ($(this).val()) {
                $("#mata_pelajaran_umum").val("").trigger("change.select2");
                $("#mata_pelajaran_id").val($(this).val()).trigger("change");
            } else if (!$("#mata_pelajaran_umum").val()) {
                $("#mata_pelajaran_id").val("").trigger("change");
            }
        });

        // --- RECOMMENDATION & SLOT LOGIC ---
        function rebuildSlotEndOptions(startId, allowedSlotsArray) {
            var startIdx = startId ? slotIndex(startId) : -1;
            var curEnd = $slotEnd.val();
            $slotEnd.find("option").each(function () {
                var val = $(this).val();
                if (!val) return;
                var idx = slotIndex(val);
                var enable = true;
                if (Array.isArray(allowedSlotsArray)) {
                    enable = allowedSlotsArray.includes(String(val));
                }
                if (startIdx >= 0 && idx < startIdx) enable = false;
                if (val === curEnd) enable = true;
                $(this).prop("disabled", !enable);
            });

            var checkEnd = $slotEnd.val();
            if (checkEnd && $slotEnd.find('option[value="' + checkEnd + '"]').prop("disabled")) {
                $slotEnd.val("").trigger("change");
            }
            if ($.fn && $.fn.select2) $slotEnd.trigger("change.select2");
        }

        function applyAvailableSlotsToSelects(availableSlots) {
            availableSlotsCache = Array.isArray(availableSlots) ? availableSlots.map(String) : null;
            
            var curStart = $slotStart.val();
            var curEnd = $slotEnd.val();
            
            $slotStart.find("option").each(function () {
                var val = $(this).val();
                if (!val) return;
                var enable = availableSlotsCache ? availableSlotsCache.includes(String(val)) : true;
                if (val === curStart) enable = true;
                $(this).prop("disabled", !enable);
            });
            $slotEnd.find("option").each(function () {
                var val = $(this).val();
                if (!val) return;
                var enable = availableSlotsCache ? availableSlotsCache.includes(String(val)) : true;
                if (val === curEnd) enable = true;
                $(this).prop("disabled", !enable);
            });

            if ($.fn && $.fn.select2) {
                $slotStart.trigger("change.select2");
                $slotEnd.trigger("change.select2");
            }
        }

        function fetchRecommendations() {
            var hari = $("#hari").val();
            var kelasId = $("#kelas_id").val();
            var mpId = $("#mata_pelajaran_id").val();
            var ruangan = $("#ruangan").val();

            if (!hari || !mpId) {
                $("#recommendations").empty();
                applyAvailableSlotsToSelects(null);
                return;
            }

            $.ajax({
                url: cfg.recommendationsUrl,
                type: "GET",
                data: {
                    hari: hari,
                    kelas_id: kelasId,
                    mata_pelajaran_id: mpId,
                    ruangan: ruangan,
                    exclude_course_id: cfg.currentCourseId
                }
            }).done(function (res) {
                if (res && res.success && Array.isArray(res.available_slots)) {
                    var ids = res.available_slots.map(function (x) { return String(x.id); });
                    applyAvailableSlotsToSelects(ids);
                    renderRecommendations(res.available_slots);
                } else {
                    $("#recommendations").empty();
                }
            });
        }

        function renderRecommendations(slots) {
            var $wrap = $("#recommendations");
            $wrap.empty();
            if (!slots || !slots.length) {
                $wrap.append('<small class="text-muted">Tidak ada slot kosong.</small>');
                return;
            }

            slots.forEach(function (s) {
                var $b = $('<button type="button" class="btn btn-sm btn-outline-secondary recommendation-btn me-1 mb-1"></button>');
                $b.text(s.label + " (" + s.start + " - " + s.end + ")");
                $b.data("slot-id", String(s.id));

                $b.on("click", function () {
                    var clicked = String($(this).data("slot-id"));
                    var curStart = $slotStart.val();
                    var curEnd = $slotEnd.val();

                    if (!curStart || (curStart && curEnd)) {
                        $slotStart.val(clicked).trigger("change");
                        $slotEnd.val("").trigger("change");
                    } else {
                        var si = slotIndex(curStart);
                        var ci = slotIndex(clicked);
                        if (ci >= si) {
                            $slotEnd.val(clicked).trigger("change");
                        } else {
                            $slotStart.val(clicked).trigger("change");
                            $slotEnd.val("").trigger("change");
                        }
                    }
                    markRecommendationButtons();
                });
                $wrap.append($b);
            });
            markRecommendationButtons();
        }

        function markRecommendationButtons() {
            var s = $slotStart.val();
            var e = $slotEnd.val();
            $(".recommendation-btn").each(function () {
                var id = String($(this).data("slot-id"));
                $(this).removeClass("active-start active-end btn-primary text-white btn-outline-primary");
                if (s && id === s) $(this).addClass("active-start btn-primary text-white");
                if (e && id === e) $(this).addClass("active-end btn-primary text-white");
            });
        }

        function calculateSlotEnd() {
            var mpId = $("#mata_pelajaran_id").val();
            var sStart = $slotStart.val();
            if (!mpId || !sStart) return;

            var jp = 1;
            var optUmum = $("#mata_pelajaran_umum").find('option:selected');
            var optJurusan = $("#mata_pelajaran_jurusan").find('option:selected');

            if (optUmum.length && optUmum.val()) {
                jp = parseInt(optUmum.attr('data-jp')) || 1;
            } else if (optJurusan.length && optJurusan.val()) {
                jp = parseInt(optJurusan.attr('data-jp')) || 1;
            }

            var sIndex = slotIndex(sStart);
            if (sIndex < 0) return;

            var currentJp = 0;
            var eIndex = sIndex;
            for (var i = sIndex; i < slotIds.length; i++) {
                var sid = slotIds[i];
                var details = slotDetails[sid];
                if (details && details.selectable) {
                    currentJp++;
                    eIndex = i;
                }
                if (currentJp >= jp) break;
            }

            var calcEndId = slotIds[eIndex];

            // Add warning container if it doesn't exist
            if ($('#jp-warning').length === 0) {
                $slotEnd.closest('.mb-3').append('<div id="jp-warning" class="mt-2 text-danger small" style="display:none;"></div>');
            }

            if (currentJp < jp) {
                $('#jp-warning').html('<i class="bi bi-x-circle me-1"></i> Sisa slot pada hari ini ('+currentJp+' JP) tidak mencukupi untuk ' + jp + ' JP.').show();
                $slotEnd.val("").trigger('change');
                return;
            } else {
                $('#jp-warning').hide();
            }

            if (calcEndId) {
                var opt = $slotEnd.find('option[value="' + calcEndId + '"]');
                opt.prop("disabled", false);
                $slotEnd.val(calcEndId).trigger('change');
            }
        }

        $("#hari").on("change", fetchRecommendations);
        $("#mata_pelajaran_id").on("change", function() {
            fetchRecommendations();
            calculateSlotEnd();
        });
        $slotStart.on("change", function () {
            rebuildSlotEndOptions($(this).val(), availableSlotsCache);
            calculateSlotEnd();
            markRecommendationButtons();
            checkConflictsLive();
        });
        $slotEnd.on("change", function(){
            markRecommendationButtons();
            checkConflictsLive();
        });

        if (cfg.initialHari) setTimeout(fetchRecommendations, 200);

        // --- CONFLICT LOGIC ---
        function checkConflictsLive() {
            var hari = $("#hari").val();
            var r = $("#ruangan").val();
            var k = $("#kelas_id").val();
            var s = $slotStart.val();
            var e = $slotEnd.val();
            var m = $("#mata_pelajaran_id").val();

            if (!hari || !r || !s || !e || !m) {
                $("#live-conflict-warning").empty();
                return;
            }

            $.ajax({
                url: cfg.conflictUrl,
                type: "POST",
                data: {
                    hari: hari, ruangan: r, kelas_id: k,
                    slot_start: s, slot_end: e, mata_pelajaran_id: m,
                    exclude_course_id: cfg.currentCourseId,
                    _token: $('meta[name="csrf-token"]').attr("content")
                }
            }).done(function (res) {
                if (res && res.has_conflict) renderConflictWarning(res.conflict_details);
                else $("#live-conflict-warning").empty();
            });
        }

        function renderConflictWarning(details) {
            var $box = $("#live-conflict-warning");
            $box.empty();
            var $alert = $('<div class="alert alert-warning py-2 small"><i class="bi bi-exclamation-triangle me-2"></i><strong>Bentrok Terdeteksi:</strong><ul class="mb-0 mt-1"></ul></div>');
            var $ul = $alert.find("ul");
            ["ruangan", "guru", "kelas"].forEach(function (b) {
                if (details[b]) details[b].forEach(function (c) {
                    $ul.append("<li>" + (c.kelas || "-") + " (" + c.jam_mulai.substring(0,5) + "-" + c.jam_selesai.substring(0,5) + ") " + (c.mata_pelajaran || "") + "</li>");
                });
            });
            $box.append($alert);
        }

        // --- RUANGAN LOGIC ---
        var kelasRuanganMap = cfg.kelasRuanganMap || {};
        var ruanganFromJadwal = cfg.ruanganFromJadwal || [];
        var $ruanganInput = $("#ruangan");
        var $ruanganAcList = $("#ruangan-ac-list");

        function applyRuanganFallback(kelasId) {
            var cur = ($ruanganInput.val() || "").trim();
            var fallback = (kelasId && kelasRuanganMap[kelasId]) ? kelasRuanganMap[kelasId] : null;

            var isAuto = (cur === "");
            if (!isAuto && kelasRuanganMap) {
                for (var id in kelasRuanganMap) {
                    if (String(kelasRuanganMap[id]).trim() === cur) { isAuto = true; break; }
                }
            }

            if (isAuto && fallback) {
                $ruanganInput.val(fallback);
                $("#ruangan-fallback-hint").text('✓ Otomatis: ' + fallback).show();
                $ruanganAcList.hide();
            } else if (isAuto && !fallback) {
                $("#ruangan-fallback-hint").hide();
            }
        }

        function renderRuanganAC(q) {
            q = (q || "").trim().toLowerCase();
            var fromKelas = $("#kelas_id").val() && kelasRuanganMap[$("#kelas_id").val()] ? [kelasRuanganMap[$("#kelas_id").val()]] : [];
            var pool = fromKelas.concat(Array.isArray(ruanganFromJadwal) ? ruanganFromJadwal : []);
            var filtered = pool.filter(function(r, i){
                return r && pool.indexOf(r) === i && String(r).toLowerCase().indexOf(q) !== -1;
            });

            if (!filtered.length) { $ruanganAcList.hide(); return; }
            var html = filtered.slice(0, 10).map(function(r){
                return '<li data-value="'+r+'" style="padding:.5rem .85rem;cursor:pointer;border-bottom:1px solid #f1f5f9;"><i class="bi bi-building me-2 text-muted"></i>'+r+'</li>';
            }).join('');
            $ruanganAcList.html(html).show();
        }

        $ruanganInput.on('input focus', function(){ renderRuanganAC($(this).val()); });
        $ruanganInput.on('input', function(){ if($(this).val()) $("#ruangan-fallback-hint").hide(); checkConflictsLive(); });
        $ruanganAcList.on('mousedown', 'li', function(){
            $ruanganInput.val($(this).data('value'));
            $ruanganAcList.hide();
            $("#ruangan-fallback-hint").hide();
            checkConflictsLive();
        });
        $(document).on('click', function(e){ if(!$ruanganInput.is(e.target) && !$ruanganAcList.has(e.target).length) $ruanganAcList.hide(); });

        if ($("#kelas_id").val()) applyRuanganFallback($("#kelas_id").val());
    });
})();
