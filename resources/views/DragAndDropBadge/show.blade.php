@extends('layouts.admin')

@section('title', 'All Badges')

@section('content')

<head>
	<style>
    body { background:#f8f9fa; }

    /* 4x6 inch badge */
    .badge-canvas {
        width: <?php echo $newbadge->width.'in' ?>;
        height: <?php echo $newbadge->height.'in' ?>;
        border: 2px dashed #333;
        background: white;
        position: relative;
        overflow: hidden;
    }

    .drag-item {
        padding: 8px;
        border: 1px solid #ccc;
        background: #fff;
        margin-bottom: 6px;
        cursor: grab;
        text-align: center;
    }

    .canvas-item {
        position: absolute;
        padding: 4px 8px;
        cursor: move;
        border: 1px solid #000;
        background: #e9ecef;
        user-select: none;
    }

    .canvas-item.active {
        outline: 2px solid blue;
    }

    .qr-box {
        width: 80px;
        height: 80px;
        border: 1px solid #000;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:10px;
        background:#fff;
    }

	.resize-handle {
	    position: absolute;
	    width: 8px;
	    height: 8px;
	    background: #0d6efd;
	}

	.resize-t { top: -4px; left: 50%; cursor: n-resize; transform: translateX(-50%); }
	.resize-b { bottom: -4px; left: 50%; cursor: s-resize; transform: translateX(-50%); }
	.resize-l { left: -4px; top: 50%; cursor: w-resize; transform: translateY(-50%); }
	.resize-r { right: -4px; top: 50%; cursor: e-resize; transform: translateY(-50%); }

	.resize-tl { top: -4px; left: -4px; cursor: nw-resize; }
	.resize-tr { top: -4px; right: -4px; cursor: ne-resize; }
	.resize-bl { bottom: -4px; left: -4px; cursor: sw-resize; }
	.resize-br { bottom: -4px; right: -4px; cursor: se-resize; }

    @media print {
        body * { visibility: hidden; }
        .badge-canvas, .badge-canvas * {
            visibility: visible;
        }
        .badge-canvas {
            position: absolute;
            left: 0;
            top: 0;
            border: none;
        }
    }
</style>
</head>

<div class="container-xxl flex-grow-1 container-p-y pt-0 mt-3">
   <div class="row">

        <!-- LEFT PANEL -->
        <div class="col-md-2">
            <h6>Fields</h6>
            <div class="drag-item" draggable="true" data-type="name">Name</div>
            <div class="drag-item" draggable="true" data-type="first_name">First Name</div>
            <div class="drag-item" draggable="true" data-type="last_name">Last Name</div>
            <div class="drag-item" draggable="true" data-type="company_name">Company</div>
            <div class="drag-item" draggable="true" data-type="qr_code">QR Code</div>
        </div>

        <!-- CENTER CANVAS -->
        <div class="col-md-7 text-center">
            <h6>Badge ({{$newbadge->width}} x {{$newbadge->height}} inch)</h6>
            <div class="badge-canvas mx-auto" id="canvas"></div>
            <!-- <button class="btn btn-primary mt-3" onclick="window.print()">Print / Save PDF</button> -->
        </div>

        <!-- RIGHT PANEL -->
        <div class="col-md-3">
            <h6>Properties</h6>

            <div class="mb-2">
                <label>Font Size</label>
                <input type="number" id="fontSize" class="form-control" value="14">
            </div>

            <div class="mb-2">
                <label>Text Color</label>
                <input type="color" id="fontColor" class="form-control">
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-warning" onclick="duplicateItem()">Duplicate</button>
                <button class="btn btn-danger" onclick="deleteItem()">Delete</button>
                <button class="btn btn-success" onclick="saveLayout()">Save Layout</button>
            </div>

            <pre id="output" class="bg-dark text-white p-2 mt-3 small"></pre>
        </div>

    </div>
</div>
<script>
const canvas = document.getElementById("canvas");
let activeItem = null;

/* Drag items */
document.querySelectorAll(".drag-item").forEach(el => {
    el.addEventListener("dragstart", e => {
        e.dataTransfer.setData("type", el.dataset.type);
    });
});

/* Canvas drop */
canvas.addEventListener("dragover", e => e.preventDefault());

canvas.addEventListener("drop", e => {
    e.preventDefault();
    createItem(e.dataTransfer.getData("type"), e.offsetX, e.offsetY);
});

/* Create element */
function createItem(type, x, y) {
    const el = document.createElement("div");
    el.classList.add("canvas-item");
    el.dataset.type = type;

    if (type === "qr_code") {
        el.classList.add("qr-box");
        el.innerHTML = "QR";
        //makeResizable(el);
    } else {
        el.innerHTML = `{${type}}`;
        el.style.fontSize = "14px";
        //makeResizable(el);
    }

    el.style.left = x + "px";
    el.style.top = y + "px";

    makeDraggable(el);
    makeResizable(el);
    selectItem(el);

    canvas.appendChild(el);
}

/* Drag inside canvas */
function makeDraggable(el) {
    el.onmousedown = function(e) {
        if (e.target.classList.contains("resize-handle")) return;
        selectItem(el);

        let shiftX = e.offsetX;
        let shiftY = e.offsetY;

        document.onmousemove = function(ev) {
            const r = canvas.getBoundingClientRect();
            el.style.left = ev.clientX - r.left - shiftX + "px";
            el.style.top = ev.clientY - r.top - shiftY + "px";
        };

        document.onmouseup = () => document.onmousemove = null;
    };
}

/* Resize */
function makeResizable(el) {
    const directions = ['t','b','l','r','tl','tr','bl','br'];

    directions.forEach(dir => {
        const handle = document.createElement('div');
        handle.className = `resize-handle resize-${dir}`;
        el.appendChild(handle);

        handle.onmousedown = function (e) {
            e.stopPropagation();

            const startX = e.clientX;
            const startY = e.clientY;
            const startW = el.offsetWidth;
            const startH = el.offsetHeight;
            const startL = el.offsetLeft;
            const startT = el.offsetTop;

            document.onmousemove = function (ev) {
                const dx = ev.clientX - startX;
                const dy = ev.clientY - startY;

                if (dir.includes('r')) el.style.width = startW + dx + 'px';
                if (dir.includes('l')) {
                    el.style.width = startW - dx + 'px';
                    el.style.left = startL + dx + 'px';
                }
                if (dir.includes('b')) el.style.height = startH + dy + 'px';
                if (dir.includes('t')) {
                    el.style.height = startH - dy + 'px';
                    el.style.top = startT + dy + 'px';
                }
            };

            document.onmouseup = () => document.onmousemove = null;
        };
    });
}

/* Select item */
function selectItem(el) {
    document.querySelectorAll(".canvas-item").forEach(i => i.classList.remove("active"));
    activeItem = el;
    el.classList.add("active");
}

/* Controls */
document.getElementById("fontSize").oninput = function() {
    if (activeItem) activeItem.style.fontSize = this.value + "px";
};

document.getElementById("fontColor").oninput = function() {
    if (activeItem) activeItem.style.color = this.value;
};

function deleteItem() {
    if (activeItem) {
        activeItem.remove();
        activeItem = null;
    }
}

function duplicateItem() {
    if (!activeItem) return;
    const clone = activeItem.cloneNode(true);
    clone.style.left = parseInt(activeItem.style.left) + 20 + "px";
    clone.style.top = parseInt(activeItem.style.top) + 20 + "px";
    makeDraggable(clone);
    makeResizable(clone);
    selectItem(clone);
    canvas.appendChild(clone);
}

/* Save Layout */
function saveLayout() {
    const data = [];
    document.querySelectorAll(".canvas-item").forEach(el => {
        data.push({
            type: el.dataset.type,
            x: el.style.left,
            y: el.style.top,
            width: el.offsetWidth,
            height: el.offsetHeight,
            fontSize: el.style.fontSize,
            color: el.style.color
        });
    });
   document.getElementById("output").textContent = JSON.stringify(data, null, 2);

    fetch('/admin/newbadges/{{$newbadge->id}}/save-layout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        },
        body: JSON.stringify({
            elements: data
        })
    }).then(response => response.json()).then(res => {
        alert('Layout saved successfully');
        console.log(res);
    }).catch(err => {
        console.error('Error saving layout:', err);
    });
}
</script>
@endsection