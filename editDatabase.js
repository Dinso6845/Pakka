document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("data-table");
    const modal = document.getElementById("editModal");
    const deleteModal = document.getElementById("deleteModal"); 
    const closeBtn = document.getElementsByClassName("close")[0];
    const editForm = document.getElementById("editForm");
    const deleteConfirmBtn = document.getElementById("deleteConfirmBtn");
    const deleteCancelBtn = document.getElementById("deleteCancelBtn"); 
    let deleteSN = null;

    // ฟังก์ชันโหลดข้อมูลทั้งหมดหรือค้นหาตามคำค้นหา
    function loadData(searchQuery = '') {
        let url = `../backend/editDatabase.php`; 
        if (searchQuery) {
            url += `?search=${encodeURIComponent(searchQuery)}`; 
        }

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    renderTable(data);
                } catch (error) {
                    console.error("Error parsing JSON:", error);
                    tableBody.innerHTML = `<tr><td colspan="7">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>`;
                }
            })
            .catch(error => {
                console.error("Error loading data:", error);
                tableBody.innerHTML = `<tr><td colspan="7">เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>`;
            });
    }

    // ฟังก์ชันแสดงข้อมูลในตาราง
    function renderTable(data) {
        if (data.length > 0) {
            tableBody.innerHTML = "";
            data.forEach((row, index) => {
                // ตรวจสอบว่า DatePoint เป็น null หรือไม่
                let formattedDate = "";
                if (row.DatePoint && row.DatePoint !== '0000-00-00 00:00:00') {
                    const timestamp = new Date(row.DatePoint);
                    formattedDate = timestamp.toLocaleDateString("th-TH", {
                        day: "2-digit",
                        month: "2-digit",
                        year: "numeric"
                    });
                } else {
                    formattedDate = '00/00/0000';
                }

                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${formattedDate}</td> 
                    <td>${row.Roomno}</td>
                    <td>${row.SN}</td>
                    <td>${row.em_month}</td>
                    <td>
                        <button onclick="editRow('${row.Roomno}', '${row.SN}', '${row.em_month}')" class="edit-btn"><i class="fas fa-pencil-alt"></i></button>
                        <button onclick="showDeleteModal(${row.SN})" class="delete-btn"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="7">ไม่พบข้อมูลที่ค้นหา</td></tr>`;
        }
    }

    // ฟังก์ชันค้นหาอัตโนมัติเมื่อพิมพ์
    document.getElementById("searchInput").addEventListener("input", function () {
        const search = this.value.trim();

        if (!search) {
            window.location.href = "editDatabase.html";
            return;
        }

        // ส่งคำค้นหาไปยัง PHP และดึงข้อมูล
        fetch(`../backend/editDatabase.php?search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    tableBody.innerHTML = `<tr><td colspan="7">${data.error}</td></tr>`;
                } else {
                    // หากพบข้อมูล ให้แสดงข้อมูลในตาราง
                    renderTable(data);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาดในการค้นหา");
            });
    });

    // ฟังก์ชันแก้ไขข้อมูล
    window.editRow = function (Roomno, SN, Month) {
        document.getElementById("edit_Roomno").value = Roomno;
        document.getElementById("edit_SN").value = SN;
        document.getElementById("edit_Month").value = Month;
        modal.style.display = "block";
    };

    // ปิด Modal
    closeBtn.onclick = function () {
        modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target == modal || event.target == deleteModal) {
            modal.style.display = "none";
            deleteModal.style.display = "none";
        }
    };

    // จัดการการส่งฟอร์มแก้ไข
    editForm.onsubmit = function (e) {
        e.preventDefault();
        const formData = new FormData(editForm);
        const data = {};
        formData.forEach((value, key) => data[key] = value);

        fetch("../backend/updateData.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(result => {
                if (result.status === "success") {
                    alert(result.message);
                    modal.style.display = "none";
                    loadData();
                } else {
                    alert(result.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาดในการอัพเดทข้อมูล");
            });
    };

    // ฟังก์ชันแสดง Modal สำหรับลบข้อมูล
    window.showDeleteModal = function (SN) {
        deleteSN = SN; 
        deleteModal.style.display = "block";
    };

    // ฟังก์ชันยืนยันการลบ
    deleteConfirmBtn.onclick = function () {
        fetch(`../backend/deleteData.php`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `SN=${deleteSN}`,
        })
            .then(response => response.text())
            .then(result => {
                alert(result);
                deleteModal.style.display = "none"; 
                loadData();
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาดในการลบข้อมูล");
                deleteModal.style.display = "none"; 
            });
    };

    // ฟังก์ชันยกเลิกการลบ
    deleteCancelBtn.onclick = function () {
        deleteModal.style.display = "none"; 
    };

    const baseURL = "http://127.0.0.1/Electricity/frontend/editDatabase.html";
    const encodedURL = encodeURIComponent(baseURL);
    window.history.replaceState({}, "", `?url=${encodedURL}`);

    loadData(); 
});
