document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("data-table");
    const modal = document.getElementById("editModal");
    const closeBtn = document.getElementsByClassName("close")[0];
    const editForm = document.getElementById("editForm");
    const errorMessage = document.getElementById("error-message");

    // ฟังก์ชันโหลดข้อมูลทั้งหมดหรือค้นหาตามคำค้นหา
    function loadData(searchQuery = '') {
        let url = `../backend/editDatabase.php`;  // แก้ไขเป็น path ของไฟล์ PHP ที่รองรับการค้นหา
        if (searchQuery) {
            url += `?search=${encodeURIComponent(searchQuery)}`;  // ส่งคำค้นหาผ่าน URL
        }

        fetch(url)
            .then(response => response.text())
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
            data.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${row.em_id}</td>
                    <td>${row.em_timestamp}</td>
                    <td>${row.em_roomNo}</td>
                    <td>${row.em_meterID}</td>
                    <td>${row.em_addNumber}</td>
                    <td>${row.em_sum}</td>
                    <td>
                        <button onclick="editRow(${row.em_id}, '${row.em_roomNo}', '${row.em_meterID}', '${row.em_addNumber}')" class="edit-btn">แก้ไข</button>
                        <button onclick="deleteRow(${row.em_id})" class="delete-btn">ลบ</button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="7">ไม่พบข้อมูลที่ค้นหา</td></tr>`;
        }
    }

    // ฟังก์ชันค้นหาอัตโนมัติเมื่อพิมพ์
    document.getElementById("searchInput").addEventListener("input", function() {
        const search = this.value.trim();

        if (!search) {
            // ถ้าไม่มีคำค้นหาให้กลับไปหน้า editdatabase.html
            window.location.href = "editdatabase.html";
            return;
        }

        // ส่งคำค้นหาไปยัง PHP และดึงข้อมูล
        fetch(`../backend/editdatabase.php?search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // แสดงข้อความหากไม่พบข้อมูล
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
    window.editRow = function(id, roomNo, meterID, addNumber) {
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_roomNo").value = roomNo;
        document.getElementById("edit_meterID").value = meterID;
        document.getElementById("edit_addNumber").value = addNumber;
        modal.style.display = "block";
    };

    // ปิด Modal
    closeBtn.onclick = function() {
        modal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    // จัดการการส่งฟอร์มแก้ไข
    editForm.onsubmit = function(e) {
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
                    loadData();  // รีเฟรชข้อมูลใหม่หลังจากอัพเดต
                } else {
                    alert(result.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("เกิดข้อผิดพลาดในการอัพเดทข้อมูล");
            });
    };

     // ฟังก์ชันลบข้อมูล
     window.deleteRow = function(id) {
        if (confirm("คุณต้องการลบข้อมูลนี้ใช่หรือไม่?")) {
            fetch(`../backend/deleteData.php`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `id=${id}`,
            })
                .then(response => response.text())
                .then(result => {
                    alert(result);
                    loadData();
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("เกิดข้อผิดพลาดในการลบข้อมูล");
                });
        }
    };


    // โหลดข้อมูลครั้งแรก
    loadData();  // เรียกใช้เพื่อโหลดข้อมูลตั้งต้น
});
    