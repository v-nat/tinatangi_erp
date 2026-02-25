<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flowers for Lily</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
            background-color: #87CEEB;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        #canvas-container {
            width: 100vw;
            height: 100vh;
            display: block;
        }
        #loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 1.5rem;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
            pointer-events: none;
            transition: opacity 0.5s ease;
        }
        #ui-panel {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            border-radius: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            pointer-events: none;
            backdrop-filter: blur(5px);
        }
        h1 {
            margin: 0;
            font-size: 1.2rem;
            color: #d1497b;
        }
        p {
            margin: 5px 0 0 0;
            font-size: 0.9rem;
            color: #555;
        }

        /* Responsive adjustments for Tablets like Redmi Tab Pro 5G */
        @media (max-width: 1024px) {
            #ui-panel {
                width: 85%;
                padding: 15px 20px;
                bottom: 30px;
            }
            h1 { font-size: 1.4rem; }
            p { font-size: 1.1rem; }
        }
    </style>
    <!-- Include Three.js and OrbitControls -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
</head>
<body>
    <div id="loading">Planting the garden...</div>
    <div id="canvas-container"></div>
    <div id="ui-panel">
        <h1>Flowers for Lily</h1>
        <p>Drag to rotate &bull; Scroll to zoom</p>
    </div>

    <script>
        let scene, camera, renderer, controls;
        let hemiLight, dirLight;
        const magicLights = []; // Array to hold the 4 spotlights
        let bgDay, bgNight, fogDay, fogNight;
        const flowers = []; // To animate them later
        let particles;

        // Bouquet Animation Variables
        let bouquetGroup;
        let clickMeSprite; // Global variable for the floating dialogue bubble
        const posBench = new THREE.Vector3();
        const rotBench = new THREE.Quaternion();
        const posCenter = new THREE.Vector3(0, 8, 0);
        const rotCenter = new THREE.Quaternion();

        // Magic Mode Variables
        let isNightMagic = false;
        let isDayMagic = false;
        let floatProgress = 0; // Bouquet floating, butterflies, pink particles
        let nightProgress = 0; // Darkening, fireflies, hearts, fireworks
        let nightMagicTimeout;
        let dayMagicTimeout;
        let butterflies = [];
        let fireflies, floatingHearts, pinkParticles;
        const fireworks = [];
        const raycaster = new THREE.Raycaster();
        const mouse = new THREE.Vector2();

        // OPTIMIZATION: Array to store ONLY clickable objects (Massive performance boost for touch/mouse)
        const interactiveObjects = [];

        // Constants for colors - updated to softer baby pinks like the reference
        const PINK_PALETTE = [0xffb6c1, 0xffc0cb, 0xf8c8dc, 0xf4c2c2, 0xff69b4];
        const GARDEN_PALETTE = [0xff0000, 0xffff00, 0xff8c00, 0x8a2be2, 0xffffff, ...PINK_PALETTE];

        // Global helper for terrain height so grass, trees, and flowers align perfectly
        function getTerrainHeight(x, z) {
            if (Math.abs(x) <= 10 && Math.abs(z) <= 10) return 0; // Keep the center flat for the bouquet
            return Math.sin(x * 0.1) * Math.cos(z * 0.1) * 1.5;
        }

        window.onload = function() {
            init();
            animate();
            document.getElementById('loading').style.opacity = '0';
        };

        function init() {
            const container = document.getElementById('canvas-container');

            // 1. Scene Setup
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0xaecde0); // Softer sky blue
            scene.fog = new THREE.FogExp2(0xaecde0, 0.015);

            // 2. Camera Setup
            camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
            camera.position.set(0, 12, 35); // Pulled back slightly to view the grander scale

            // 3. Renderer Setup (Optimized for High-Res Tablets)
            renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: "high-performance" });
            renderer.setSize(window.innerWidth, window.innerHeight);
            // OPTIMIZATION: Cap pixel ratio to 1.5 to prevent massive lag on high-DPI tablets (like Redmi Tab Pro)
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            container.appendChild(renderer.domElement);

            // 4. Controls Setup
            controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.maxPolarAngle = Math.PI / 2 - 0.05;
            controls.minDistance = 5;
            controls.maxDistance = 120; // Allow zooming out further for massive arch
            controls.target.set(0, 8, 0); // Look higher up at the center arch

            // Setup Colors for Day/Night Transition
            bgDay = new THREE.Color(0xaecde0);
            bgNight = new THREE.Color(0x0a0a1a);
            fogDay = new THREE.Color(0xaecde0);
            fogNight = new THREE.Color(0x050510);

            // 5. Lighting Setup
            hemiLight = new THREE.HemisphereLight(0xffffff, 0x446644, 0.8);
            hemiLight.position.set(0, 50, 0);
            scene.add(hemiLight);

            dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
            dirLight.position.set(-20, 30, 20);
            dirLight.castShadow = true;
            // OPTIMIZATION: Lower shadow map resolution slightly for smooth mobile/tablet performance
            dirLight.shadow.mapSize.width = 1024;
            dirLight.shadow.mapSize.height = 1024;
            dirLight.shadow.camera.near = 0.5;
            dirLight.shadow.camera.far = 100;
            dirLight.shadow.camera.left = -30;
            dirLight.shadow.camera.right = 30;
            dirLight.shadow.camera.top = 30;
            dirLight.shadow.camera.bottom = -30;
            scene.add(dirLight);

            // Create 4 magic spotlights from different angles
            const lightPositions = [
                { x: 0, y: 45, z: 20 },   // Front
                { x: -30, y: 40, z: 0 },  // Left
                { x: 30, y: 40, z: 0 },   // Right
                { x: 0, y: 45, z: -20 }   // Back
            ];

            lightPositions.forEach(pos => {
                let spotLight = new THREE.SpotLight(0xffb6c1, 0); // Soft pink spotlight
                spotLight.position.set(pos.x, pos.y, pos.z);
                spotLight.angle = Math.PI / 5;
                spotLight.penumbra = 0.5;
                spotLight.castShadow = true;
                scene.add(spotLight);
                scene.add(spotLight.target);
                spotLight.target.position.set(0, 5, 0); // Initial target
                magicLights.push(spotLight);
            });

            // 6. Environment (Ground)
            createGround();

            // 6.5 Add dense grass cover
            createInstancedGrass();

            // 7. Create Main Pink Bouquet
            createMainBouquet();

            // 8. Create Surrounding Garden
            createGarden();

            // 8.5 Add Cherry Blossom Trees to Perimeter
            createCherryTrees();

            // 8.7 Create the Pink Flower Arch Stage
            createArch();

            // 9. Add magical floating pollen/particles
            createParticles();

            // 10. Add Bench and Chalkboard
            createBench();
            createChalkboard();

            // 10.5 Setup Initial Bouquet Bench Position (Adjusted for slightly smaller bouquet)
            posBench.set(-20, getTerrainHeight(-20, 12) + 4.6, 12);
            // Lean back on the bench, turned slightly
            rotBench.setFromEuler(new THREE.Euler(0.7, Math.PI / 4 + 0.3, 0.2));

            // 11. Add Magic Elements (Hidden initially)
            createButterflies();
            createFireflies();
            createFloatingHearts();
            createPinkParticles();

            // Setup Interaction
            window.addEventListener('pointerdown', onPointerDown, false);
            window.addEventListener('pointermove', onPointerMove, false);
            window.addEventListener('resize', onWindowResize, false);
        }

        function createGround() {
            const groundGeo = new THREE.PlaneGeometry(200, 200, 32, 32);

            const vertices = groundGeo.attributes.position.array;
            for (let i = 0; i < vertices.length; i += 3) {
                const x = vertices[i];
                const y = vertices[i+1];
                // 'y' here maps to world 'z' after the plane is rotated
                vertices[i+2] = getTerrainHeight(x, y);
            }
            groundGeo.computeVertexNormals();

            const groundMat = new THREE.MeshStandardMaterial({
                color: 0x2e5c2e, // Darkened slightly to act as undergrowth beneath the grass
                roughness: 0.9,
                metalness: 0.05
            });
            const ground = new THREE.Mesh(groundGeo, groundMat);
            ground.rotation.x = -Math.PI / 2;
            ground.receiveShadow = true;
            scene.add(ground);
        }

        function createInstancedGrass() {
            // OPTIMIZATION: Dynamically adapt grass density based on screen size (Tablet vs Desktop)
            const isTablet = window.innerWidth <= 1200;
            const count = isTablet ? 35000 : 60000;

            const geo = new THREE.PlaneGeometry(0.15, 0.6);
            geo.translate(0, 0.3, 0); // Anchor to the bottom

            // Give the grass blades a natural curve
            const pos = geo.attributes.position;
            for(let i=0; i<pos.count; i++) {
                if (pos.getY(i) > 0.1) pos.setZ(i, pos.getZ(i) + 0.1);
            }
            geo.computeVertexNormals();

            const mat = new THREE.MeshLambertMaterial({color: 0x4caf50, side: THREE.DoubleSide});
            const mesh = new THREE.InstancedMesh(geo, mat, count);

            const dummy = new THREE.Object3D();
            const color = new THREE.Color();

            let i = 0;
            while(i < count) {
                const x = (Math.random() - 0.5) * 160;
                const z = (Math.random() - 0.5) * 160;

                // Keep the area immediately around the new central stage clear
                if(x*x + z*z < 95) continue;

                const y = getTerrainHeight(x, z);

                dummy.position.set(x, y, z);
                dummy.rotation.y = Math.random() * Math.PI;
                dummy.rotation.x = (Math.random() - 0.5) * 0.2; // Slight random lean
                dummy.rotation.z = (Math.random() - 0.5) * 0.2;

                const scale = 0.5 + Math.random() * 0.8;
                dummy.scale.set(scale, scale, scale);

                dummy.updateMatrix();
                mesh.setMatrixAt(i, dummy.matrix);

                // Varied greens with slight yellow/blue tints for realism
                color.setHSL(0.28 + Math.random()*0.08, 0.5 + Math.random()*0.3, 0.3 + Math.random()*0.2);
                mesh.setColorAt(i, color);

                i++;
            }
            mesh.instanceMatrix.needsUpdate = true;
            if (mesh.instanceColor) mesh.instanceColor.needsUpdate = true;
            mesh.receiveShadow = true;
            scene.add(mesh);
        }

        function createArch() {
            const archGroup = new THREE.Group();

            // Stone stage base (Expanded further to match even wider arch)
            const stageGeo = new THREE.CylinderGeometry(26, 27, 1.2, 48);
            const stageMat = new THREE.MeshStandardMaterial({color: 0xdddddd, roughness: 0.9});
            const stage = new THREE.Mesh(stageGeo, stageMat);
            stage.position.y = 0.6;
            stage.receiveShadow = true;
            archGroup.add(stage);

            // Arch Curve Path (Significantly taller and wider)
            class ArchCurve extends THREE.Curve {
                getPoint(t, optionalTarget = new THREE.Vector3()) {
                    const angle = Math.PI * t; // half circle from 0 to PI
                    const x = Math.cos(angle) * 22; // Expanded from 16 to 22
                    const y = Math.sin(angle) * 35; // Expanded from 28 to 35
                    return optionalTarget.set(x, y, 0);
                }
            }
            const path = new ArchCurve();
            const tubeGeo = new THREE.TubeGeometry(path, 128, 0.9, 12, false);
            const woodMat = new THREE.MeshLambertMaterial({color: 0x4a3b32});
            const archBase = new THREE.Mesh(tubeGeo, woodMat);
            archGroup.add(archBase);

            // Add Intertwining Vines
            class VineCurve extends THREE.Curve {
                constructor(offsetAngle) { super(); this.offsetAngle = offsetAngle; }
                getPoint(t, optionalTarget = new THREE.Vector3()) {
                    const angle = Math.PI * t;
                    const bx = Math.cos(angle) * 22; // Match new width
                    const by = Math.sin(angle) * 35; // Match new height
                    const twist = t * Math.PI * 26 + this.offsetAngle; // Number of wraps

                    const nx = Math.cos(angle);
                    const ny = Math.sin(angle);

                    const ox = bx + nx * Math.cos(twist) * 1.3;
                    const oy = by + ny * Math.cos(twist) * 1.3;
                    const oz = Math.sin(twist) * 1.3;
                    return optionalTarget.set(ox, oy, oz);
                }
            }

            const vineMat = new THREE.MeshLambertMaterial({color: 0x2e5c2e});
            const leafGeo = new THREE.PlaneGeometry(0.6, 1.0);
            leafGeo.translate(0, 0.5, 0);
            const leafMat = new THREE.MeshLambertMaterial({color: 0x4caf50, side: THREE.DoubleSide});

            // Create two vines wrapping around the arch
            [0, Math.PI].forEach(offset => {
                const vinePath = new VineCurve(offset);
                const vine = new THREE.Mesh(new THREE.TubeGeometry(vinePath, 256, 0.15, 6, false), vineMat);
                vine.castShadow = true;
                archGroup.add(vine);

                // Add leaves to the vines
                const leaves = new THREE.InstancedMesh(leafGeo, leafMat, 180); // Increased density
                const dummy = new THREE.Object3D();
                for(let i=0; i<180; i++) {
                    const t = i / 180;
                    const pt = vinePath.getPoint(t);
                    const tangent = vinePath.getTangent(t);
                    dummy.position.copy(pt);
                    dummy.quaternion.setFromUnitVectors(new THREE.Vector3(0,1,0), tangent);
                    dummy.rotateX(Math.random() * Math.PI);
                    dummy.updateMatrix();
                    leaves.setMatrixAt(i, dummy.matrix);
                }
                archGroup.add(leaves);
            });

            // Pink flowers clustering along the arch (Increased count for much larger arch)
            const flowerMat = new THREE.MeshStandardMaterial({
                color: 0xffb6c1,
                roughness: 0.7,
                flatShading: true
            });

            for(let i=0; i<=130; i++) {
                const t = i / 130;
                const pt = path.getPoint(t);
                const clusterGeo = new THREE.DodecahedronGeometry(2.0 + Math.random()*1.5, 1);
                const pos = clusterGeo.attributes.position;
                for(let k=0; k<pos.count; k++) {
                    pos.setX(k, pos.getX(k) + (Math.random()-0.5)*0.8);
                    pos.setY(k, pos.getY(k) + (Math.random()-0.5)*0.8);
                    pos.setZ(k, pos.getZ(k) + (Math.random()-0.5)*0.8);
                }
                clusterGeo.computeVertexNormals();
                const cluster = new THREE.Mesh(clusterGeo, flowerMat);
                cluster.position.copy(pt);
                cluster.position.x += (Math.random()-0.5)*2.5;
                cluster.position.y += (Math.random()-0.5)*2.5;
                cluster.position.z += (Math.random()-0.5)*2.5;
                cluster.castShadow = true;
                archGroup.add(cluster);
            }

            archGroup.position.set(0, 0, -5); // Pushed slightly back so bouquet hovers inside seamlessly
            scene.add(archGroup);
        }

        function createCherryTrees() {
            const numTrees = 30;
            const radiusMin = 65; // Pushed out further to make room for the massive arch
            const radiusMax = 120;

            const trunkMat = new THREE.MeshLambertMaterial({color: 0x4a3b32});
            const leafMat = new THREE.MeshStandardMaterial({
                color: 0xffb7c5,
                roughness: 0.9,
                metalness: 0.1,
                flatShading: true
            });

            for(let i=0; i<numTrees; i++) {
                const tree = new THREE.Group();

                // Trunk (Scaled up to realistic massive tree heights)
                const trunkHeight = 20 + Math.random() * 15;
                const trunkGeo = new THREE.CylinderGeometry(1.0, 1.5, trunkHeight, 7);
                const trunk = new THREE.Mesh(trunkGeo, trunkMat);
                trunk.position.y = trunkHeight / 2;
                trunk.castShadow = true;
                trunk.receiveShadow = true;
                tree.add(trunk);

                // Fluffy pink canopy clusters (Scaled up)
                const numClusters = 7 + Math.floor(Math.random() * 6);
                for(let j=0; j<numClusters; j++) {
                    const clusterGeo = new THREE.DodecahedronGeometry(6 + Math.random() * 4.0, 1);
                    const pos = clusterGeo.attributes.position;
                    for(let k=0; k<pos.count; k++) {
                        pos.setX(k, pos.getX(k) + (Math.random()-0.5)*1.5);
                        pos.setY(k, pos.getY(k) + (Math.random()-0.5)*1.5);
                        pos.setZ(k, pos.getZ(k) + (Math.random()-0.5)*1.5);
                    }
                    clusterGeo.computeVertexNormals();

                    const cluster = new THREE.Mesh(clusterGeo, leafMat);
                    const cy = trunkHeight * 0.5 + Math.random() * trunkHeight * 0.5;
                    const cx = (Math.random() - 0.5) * 12;
                    const cz = (Math.random() - 0.5) * 12;

                    cluster.position.set(cx, cy, cz);
                    cluster.castShadow = true;
                    tree.add(cluster);
                }

                // Place in a perimeter ring
                const angle = (i / numTrees) * Math.PI * 2 + (Math.random() * 0.3);
                const dist = radiusMin + Math.random() * (radiusMax - radiusMin);

                const tx = Math.cos(angle) * dist;
                const tz = Math.sin(angle) * dist;
                const ty = getTerrainHeight(tx, tz);

                tree.position.set(tx, ty - 0.5, tz); // Plant slightly into ground
                tree.rotation.y = Math.random() * Math.PI * 2;
                tree.rotation.x = (Math.random() - 0.5) * 0.15;
                tree.rotation.z = (Math.random() - 0.5) * 0.15;

                scene.add(tree);
            }
        }

        function createTulip(flowerColor, isBouquet = false) {
            const group = new THREE.Group();
            const heightVariance = Math.random() * 1.5;
            const stemHeight = isBouquet ? 6 + heightVariance * 0.5 : 3 + heightVariance;

            // Stem
            const stemGeo = new THREE.CylinderGeometry(0.04, 0.06, stemHeight, 8);
            const stemMat = new THREE.MeshLambertMaterial({ color: 0x68a068 });
            const stem = new THREE.Mesh(stemGeo, stemMat);
            stem.position.y = stemHeight / 2;
            stem.castShadow = true;
            group.add(stem);

            // Leaves
            const leafGeo = new THREE.SphereGeometry(0.5, 16, 16);
            leafGeo.scale(0.3, 1.8, 0.05);
            leafGeo.translate(0, 0.9, 0);
            const leafMat = new THREE.MeshLambertMaterial({ color: 0x4caf50, side: THREE.DoubleSide });

            const leaf1 = new THREE.Mesh(leafGeo, leafMat);
            leaf1.position.y = stemHeight * 0.2;
            leaf1.rotation.z = Math.PI / 8 + Math.random() * 0.2;
            leaf1.rotation.y = Math.random() * Math.PI;
            group.add(leaf1);

            const leaf2 = new THREE.Mesh(leafGeo, leafMat);
            leaf2.position.y = stemHeight * 0.4;
            leaf2.rotation.z = -(Math.PI / 7 + Math.random() * 0.2);
            leaf2.rotation.y = Math.random() * Math.PI;
            group.add(leaf2);

            // Flower Head
            const head = new THREE.Group();
            const petalMat = new THREE.MeshStandardMaterial({
                color: flowerColor,
                side: THREE.DoubleSide,
                roughness: 0.5,
                metalness: 0.1
            });

            for (let i = 0; i < 6; i++) {
                const petalGeo = new THREE.SphereGeometry(0.5, 16, 16, 0, Math.PI);
                petalGeo.scale(0.6, 1.4, 0.2);
                petalGeo.translate(0, 0.7, 0);

                const petal = new THREE.Mesh(petalGeo, petalMat);
                petal.castShadow = true;

                const angle = (i / 6) * Math.PI * 2;
                petal.rotation.y = angle;

                if (i % 2 === 0) {
                    petal.position.x = Math.cos(angle) * 0.08;
                    petal.position.z = Math.sin(angle) * 0.08;
                    petal.rotation.z = 0.1;
                } else {
                    petal.position.x = Math.cos(angle) * 0.15;
                    petal.position.z = Math.sin(angle) * 0.15;
                    petal.rotation.z = 0.25;
                }
                head.add(petal);
            }

            const stamenGeo = new THREE.CylinderGeometry(0.1, 0.05, 0.6, 8);
            const stamenMat = new THREE.MeshLambertMaterial({ color: 0xffd700 });
            const stamen = new THREE.Mesh(stamenGeo, stamenMat);
            stamen.position.y = 0.3;
            head.add(stamen);

            head.position.y = stemHeight;
            group.add(head);

            group.userData = {
                originalRotX: group.rotation.x,
                originalRotZ: group.rotation.z,
                randomSeed: Math.random() * 100,
                swaySpeed: 0.001 + Math.random() * 0.001,
                isBouquet: isBouquet
            };

            return group;
        }

        // Procedural generator for small white daisy filler flowers
        function createDaisy() {
            const group = new THREE.Group();

            const stemHeight = 5 + Math.random() * 2;
            const stemGeo = new THREE.CylinderGeometry(0.02, 0.03, stemHeight, 5);
            const stemMat = new THREE.MeshLambertMaterial({ color: 0x81c784 });
            const stem = new THREE.Mesh(stemGeo, stemMat);
            stem.position.y = stemHeight / 2;
            group.add(stem);

            const head = new THREE.Group();
            head.position.y = stemHeight;

            // Yellow center
            const centerGeo = new THREE.SphereGeometry(0.12, 8, 8);
            centerGeo.scale(1, 0.4, 1);
            const centerMat = new THREE.MeshLambertMaterial({color: 0xffeb3b});
            const center = new THREE.Mesh(centerGeo, centerMat);
            head.add(center);

            // White Petals
            const petalMat = new THREE.MeshLambertMaterial({color: 0xffffff, side: THREE.DoubleSide});
            for(let i=0; i<12; i++) {
                const petalGeo = new THREE.SphereGeometry(0.06, 8, 8);
                petalGeo.scale(1, 4.5, 0.1);
                petalGeo.translate(0, 0.25, 0);
                const petal = new THREE.Mesh(petalGeo, petalMat);

                const angle = (i / 12) * Math.PI * 2;
                petal.rotation.y = angle;
                petal.rotation.x = Math.PI / 2 - 0.15 + Math.random()*0.1; // Splayed outwards

                head.add(petal);
            }

            group.add(head);

            group.userData = {
                originalRotX: group.rotation.x,
                originalRotZ: group.rotation.z,
                randomSeed: Math.random() * 100,
                swaySpeed: 0.0015 + Math.random() * 0.001,
                isBouquet: true
            };

            return group;
        }

        // Procedural generator for bending tall grass
        function createTallGrass(height, thickness, color, isBouquet = false) {
            const geo = new THREE.CylinderGeometry(0.01, thickness, height, 4, 6);
            const pos = geo.attributes.position;

            const bendDirX = (Math.random() - 0.5) * 1.5;
            const bendDirZ = (Math.random() - 0.5) * 1.5;

            for(let i=0; i<pos.count; i++) {
                const y = pos.getY(i);
                const ny = (y + height/2) / height;
                const bendAmt = Math.pow(ny, 2) * 1.0;

                pos.setX(i, pos.getX(i) + bendDirX * bendAmt);
                pos.setZ(i, pos.getZ(i) + bendDirZ * bendAmt);
            }
            geo.computeVertexNormals();
            geo.translate(0, height/2, 0); // Anchor at base

            const mat = new THREE.MeshLambertMaterial({ color: color, side: THREE.DoubleSide });
            const mesh = new THREE.Mesh(geo, mat);
            mesh.castShadow = true;

            mesh.userData = {
                originalRotX: mesh.rotation.x,
                originalRotZ: mesh.rotation.z,
                randomSeed: Math.random() * 100,
                swaySpeed: 0.002 + Math.random() * 0.001,
                isBouquet: isBouquet
            };

            return mesh;
        }

        // Procedural generator for broad green leaves seen in the bouquet
        function createBroadLeaf() {
            const geo = new THREE.PlaneGeometry(0.8, 4.0, 4, 8);
            const pos = geo.attributes.position;
            for(let i=0; i<pos.count; i++) {
                const y = pos.getY(i);
                // Taper top and bottom, curve width
                const ny = (y + 2.0) / 4.0;
                const width = Math.sin(ny * Math.PI);
                pos.setX(i, pos.getX(i) * width);
                // Bend back slightly
                pos.setZ(i, Math.pow(ny, 2) * -0.8);
            }
            geo.computeVertexNormals();
            geo.translate(0, 2.0, 0); // Anchor at bottom
            const mat = new THREE.MeshLambertMaterial({color: 0x4caf50, side: THREE.DoubleSide});
            const mesh = new THREE.Mesh(geo, mat);
            mesh.userData = {
                originalRotX: mesh.rotation.x, originalRotZ: mesh.rotation.z,
                randomSeed: Math.random() * 100, swaySpeed: 0.002, isBouquet: true
            };
            return mesh;
        }

        // Procedural generator for soft, elegant wavy inner white wrapper
        function createSmoothWavyWrapper(radiusTop, radiusBottom, height, color, waves, waveDepth) {
            const geo = new THREE.CylinderGeometry(radiusTop, radiusBottom, height, 128, 1, true);
            const pos = geo.attributes.position;
            for (let i = 0; i < pos.count; i++) {
                const x = pos.getX(i);
                const z = pos.getZ(i);
                const y = pos.getY(i);
                const angle = Math.atan2(z, x);
                const ny = (y + height/2)/height;

                // Gentle, elegant sine waves, deeper at the top
                const wave = Math.sin(angle * waves) * waveDepth * Math.pow(ny, 2.0);
                const len = Math.sqrt(x*x + z*z);
                pos.setX(i, (x/len) * (len + wave));
                pos.setZ(i, (z/len) * (len + wave));
            }
            geo.computeVertexNormals();
            const mat = new THREE.MeshPhysicalMaterial({
                color: color, side: THREE.DoubleSide, roughness: 0.9, transparent: true, opacity: 0.9
            });
            return new THREE.Mesh(geo, mat);
        }

        function createMainBouquet() {
            const bouquet = new THREE.Group();

            // Clean, uniform pink material for the wrapper
            const backMat = new THREE.MeshPhysicalMaterial({
                color: 0xf8c8dc, side: THREE.DoubleSide, roughness: 0.7, metalness: 0.05
            });

            // 1. Clean, flared back wrapper sheet (Gap perfectly facing front +Z to show flowers)
            const backWrap = new THREE.Mesh(
                new THREE.CylinderGeometry(7.0, 1.8, 7.5, 64, 1, true, Math.PI * 0.85, Math.PI * 1.3),
                backMat
            );
            backWrap.position.set(0, 4.2, -0.4);
            backWrap.rotation.x = -0.15; // Lean back slightly
            bouquet.add(backWrap);

            // 2. Soft, elegantly folded white inner layer
            const innerWrap = createSmoothWavyWrapper(5.5, 1.5, 6.0, 0xffffff, 9, 0.6);
            innerWrap.position.set(0, 4.0, -0.2);
            innerWrap.rotation.x = -0.1;
            bouquet.add(innerWrap);

            // 3. Crisp, clean overlapping front flaps
            // Right Flap (+X side, folding securely towards front)
            const flapRightGeo = new THREE.CylinderGeometry(4.8, 1.7, 5.5, 32, 1, true, -Math.PI * 0.1, Math.PI * 0.8);
            const frontRight = new THREE.Mesh(flapRightGeo, backMat);
            frontRight.position.set(0.6, 3.4, 0.4);
            frontRight.rotation.z = -0.15; // Lean right (outward)
            frontRight.rotation.x = 0.15; // Lean forward
            bouquet.add(frontRight);

            // Left Flap (-X side, folding securely towards front)
            const flapLeftGeo = new THREE.CylinderGeometry(5.0, 1.8, 5.5, 32, 1, true, Math.PI * 0.3, Math.PI * 0.8);
            const frontLeft = new THREE.Mesh(flapLeftGeo, backMat);
            frontLeft.position.set(-0.6, 3.5, 0.5);
            frontLeft.rotation.z = 0.15; // Lean left (outward)
            frontLeft.rotation.x = 0.15; // Lean forward
            bouquet.add(frontLeft);

            // 4. Base stem wrap
            const baseWrap = new THREE.Mesh(
                new THREE.CylinderGeometry(1.6, 2.2, 4.0, 32, 1, true),
                backMat
            );
            baseWrap.position.y = 1.0;
            bouquet.add(baseWrap);

            // 5. Add Ribbon & Tails
            const ribbonMat = new THREE.MeshStandardMaterial({ color: 0xfffdd0, roughness: 0.6, side: THREE.DoubleSide }); // Cream ribbon
            const knot = new THREE.Mesh(new THREE.TorusGeometry(1.7, 0.15, 16, 32), ribbonMat);
            knot.rotation.x = Math.PI / 2;
            knot.position.y = 2.5;
            bouquet.add(knot);

            const tailGeo = new THREE.PlaneGeometry(0.5, 4, 4, 16);
            const tPos = tailGeo.attributes.position;
            for(let i=0; i<tPos.count; i++) {
                const y = tPos.getY(i);
                const z = tPos.getZ(i);
                // Add soft wave to tails
                tPos.setZ(i, z + Math.sin(y * 2) * 0.2 + Math.pow(-y + 2, 2) * 0.1);
            }
            tailGeo.computeVertexNormals();
            tailGeo.translate(0, -2, 0); // Anchor at top

            const tail1 = new THREE.Mesh(tailGeo, ribbonMat);
            tail1.position.set(0.4, 2.5, 1.7);
            tail1.rotation.z = -0.15;
            tail1.rotation.y = 0.1;
            bouquet.add(tail1);

            const tail2 = new THREE.Mesh(tailGeo, ribbonMat);
            tail2.position.set(-0.4, 2.5, 1.7);
            tail2.rotation.z = 0.15;
            tail2.rotation.y = -0.1;
            bouquet.add(tail2);

            // 6. Arrange 10 Pink Tulips exactly matching the reference image
            const tulipPositions = [
                {x: 0, y: 7.0, z: -1.0, rx: 0.1, rz: 0},     // Top center
                {x: -1.8, y: 6.5, z: -0.5, rx: 0.15, rz: 0.15}, // Top left
                {x: 1.8, y: 6.5, z: -0.5, rx: 0.15, rz: -0.15}, // Top right
                {x: -2.5, y: 5.5, z: 0.2, rx: 0.2, rz: 0.25},  // Mid outer left
                {x: -1.0, y: 5.8, z: 0.5, rx: 0.15, rz: 0.1},  // Mid inner left
                {x: 1.0, y: 5.8, z: 0.5, rx: 0.15, rz: -0.1},  // Mid inner right
                {x: 2.5, y: 5.5, z: 0.2, rx: 0.2, rz: -0.25},  // Mid outer right
                {x: -1.5, y: 4.5, z: 1.2, rx: 0.3, rz: 0.2},   // Bottom left
                {x: 1.5, y: 4.5, z: 1.2, rx: 0.3, rz: -0.2},   // Bottom right
                {x: 0, y: 4.8, z: 1.5, rx: 0.25, rz: 0},     // Bottom center
            ];

            tulipPositions.forEach(pos => {
                const tulip = createTulip(0xffb6c1, true); // Use uniform baby pink
                tulip.position.set(pos.x, pos.y - 3, pos.z); // Adjust stem base height
                tulip.rotation.x = pos.rx;
                tulip.rotation.z = pos.rz;
                bouquet.add(tulip);
                flowers.push(tulip);
            });

            // Add broad green leaves matching the image
            for(let i=0; i<5; i++) {
                const leaf = createBroadLeaf();
                const angle = (i/5) * Math.PI * 2;
                leaf.position.set(Math.cos(angle)*1.0, 3.5, Math.sin(angle)*1.0);
                leaf.rotation.y = -angle + Math.PI/2;
                leaf.rotation.x = 0.2;
                bouquet.add(leaf);
                flowers.push(leaf);
            }

            // Add White Daisies clustered as filler
            for(let i=0; i<35; i++) {
                const daisy = createDaisy();
                const angle = Math.random() * Math.PI * 2;
                const r = 0.2 + Math.random() * 2.5;

                // Keep daisies slightly lower than the tulips
                daisy.position.set(Math.cos(angle)*r, 2.5 + Math.random()*2.5, Math.sin(angle)*r);
                daisy.rotation.x = Math.sin(angle) * 0.2;
                daisy.rotation.z = -Math.cos(angle) * 0.2;

                bouquet.add(daisy);
                flowers.push(daisy);
            }

            // Create and add the floating 'Click me!' dialogue bubble (Made significantly bigger & sharper)
            const bubbleCanvas = document.createElement('canvas');
            bubbleCanvas.width = 512; bubbleCanvas.height = 256;
            const bCtx = bubbleCanvas.getContext('2d');

            bCtx.fillStyle = 'rgba(255, 255, 255, 0.95)';
            bCtx.shadowColor = 'rgba(0,0,0,0.15)';
            bCtx.shadowBlur = 20;
            bCtx.beginPath();
            bCtx.moveTo(60, 40);
            bCtx.lineTo(452, 40);
            bCtx.quadraticCurveTo(492, 40, 492, 80);
            bCtx.lineTo(492, 160);
            bCtx.quadraticCurveTo(492, 200, 452, 200);
            bCtx.lineTo(280, 200);
            bCtx.lineTo(256, 230); // Pointer arrow
            bCtx.lineTo(232, 200);
            bCtx.lineTo(60, 200);
            bCtx.quadraticCurveTo(20, 200, 20, 160);
            bCtx.lineTo(20, 80);
            bCtx.quadraticCurveTo(20, 40, 60, 40);
            bCtx.fill();

            bCtx.shadowBlur = 0;
            bCtx.fillStyle = '#ff69b4';
            bCtx.font = 'bold 72px "Segoe UI", sans-serif';
            bCtx.textAlign = 'center';
            bCtx.fillText('Click me! ✨', 256, 145);

            const bubbleTex = new THREE.CanvasTexture(bubbleCanvas);
            const bubbleMat = new THREE.SpriteMaterial({ map: bubbleTex, transparent: true });
            clickMeSprite = new THREE.Sprite(bubbleMat);
            clickMeSprite.scale.set(7, 3.5, 1); // Scaled up massively
            clickMeSprite.position.set(0, 12.5, 0); // Positioned higher to accommodate the new size
            bouquet.add(clickMeSprite);

            // Slightly reduce the overall size of the bouquet
            bouquet.scale.set(0.85, 0.85, 0.85);

            // Save the group globally for animation
            bouquetGroup = bouquet;

            // Make the entire bouquet clickable for Day Magic
            bouquetGroup.traverse((child) => {
                if (child.isMesh) {
                    child.userData.isClickableBouquet = true;
                    // OPTIMIZATION: Add to raycast targets
                    interactiveObjects.push(child);
                }
            });

            scene.add(bouquetGroup);
        }

        function createGarden() {
            const numGardenElements = 400;
            const innerRadius = 8;
            const outerRadius = 45;

            for (let i = 0; i < numGardenElements; i++) {
                const angle = Math.random() * Math.PI * 2;
                const radius = innerRadius + Math.random() * (outerRadius - innerRadius);

                const x = Math.cos(angle) * radius;
                const z = Math.sin(angle) * radius;
                const y = getTerrainHeight(x, z);

                // 30% Tulips, 70% Tall Grasses for a lush look
                if (Math.random() < 0.3) {
                    const color = GARDEN_PALETTE[Math.floor(Math.random() * GARDEN_PALETTE.length)];
                    const tulip = createTulip(color, false);
                    tulip.position.set(x, y, z);
                    tulip.rotation.y = Math.random() * Math.PI * 2;
                    tulip.rotation.x += Math.cos(z * 0.1) * 0.1;
                    tulip.rotation.z -= Math.cos(x * 0.1) * 0.1;
                    tulip.userData.originalRotX = tulip.rotation.x;
                    tulip.userData.originalRotZ = tulip.rotation.z;
                    scene.add(tulip);
                    flowers.push(tulip);
                } else {
                    // Plant a tuft of tall grass
                    const grassHeight = 2 + Math.random() * 3;
                    const grass = createTallGrass(grassHeight, 0.05 + Math.random()*0.03, 0x4caf50, false);
                    grass.position.set(x, y, z);
                    grass.rotation.y = Math.random() * Math.PI * 2;
                    grass.rotation.x += Math.cos(z * 0.1) * 0.1;
                    grass.rotation.z -= Math.cos(x * 0.1) * 0.1;
                    grass.userData.originalRotX = grass.rotation.x;
                    grass.userData.originalRotZ = grass.rotation.z;
                    scene.add(grass);
                    flowers.push(grass);
                }
            }
        }

        function createParticles() {
            const particleCount = 1000;
            const particleGeo = new THREE.BufferGeometry();
            const particlePos = new Float32Array(particleCount * 3);

            for (let i = 0; i < particleCount * 3; i++) {
                particlePos[i] = (Math.random() - 0.5) * 100;
                if(i % 3 === 1) {
                    particlePos[i] = Math.random() * 15;
                }
            }

            particleGeo.setAttribute('position', new THREE.BufferAttribute(particlePos, 3));

            const particleMat = new THREE.PointsMaterial({
                color: 0xffffff,
                size: 0.15,
                transparent: true,
                opacity: 0.8,
                blending: THREE.AdditiveBlending,
                depthWrite: false // Prevents dark halos, making them glow better
            });

            particles = new THREE.Points(particleGeo, particleMat);
            scene.add(particles);
        }

        function createBench() {
            const benchGroup = new THREE.Group();
            const woodMat = new THREE.MeshLambertMaterial({color: 0x5c4033});

            const seat = new THREE.Mesh(new THREE.BoxGeometry(4, 0.2, 1.2), woodMat);
            seat.position.y = 1;
            seat.castShadow = true;
            seat.receiveShadow = true;
            benchGroup.add(seat);

            const back = new THREE.Mesh(new THREE.BoxGeometry(4, 1.0, 0.2), woodMat);
            back.position.set(0, 1.6, -0.6);
            back.castShadow = true;
            benchGroup.add(back);

            const legGeo = new THREE.BoxGeometry(0.2, 1, 1.2);
            const legL = new THREE.Mesh(legGeo, woodMat);
            legL.position.set(-1.8, 0.5, 0);
            legL.castShadow = true;
            const legR = new THREE.Mesh(legGeo, woodMat);
            legR.position.set(1.8, 0.5, 0);
            legR.castShadow = true;
            benchGroup.add(legL, legR);

            // Scale the bench to realistic life-size relative to the bouquet
            benchGroup.scale.set(4.5, 4.5, 4.5);

            // Placed naturally on the terrain
            benchGroup.position.set(-20, getTerrainHeight(-20, 12), 12);
            benchGroup.rotation.y = Math.PI / 4;
            scene.add(benchGroup);
        }

        function createChalkboard() {
            const boardGroup = new THREE.Group();
            const woodMat = new THREE.MeshLambertMaterial({color: 0x4a3b32});

            // Scaled up more than twice the size of the bouquet
            const legGeo = new THREE.CylinderGeometry(0.12, 0.12, 7);
            const legL = new THREE.Mesh(legGeo, woodMat);
            legL.position.set(-2.2, 3.5, 0);
            legL.rotation.z = 0.1;
            legL.castShadow = true;
            const legR = new THREE.Mesh(legGeo, woodMat);
            legR.position.set(2.2, 3.5, 0);
            legR.rotation.z = -0.1;
            legR.castShadow = true;
            const legB = new THREE.Mesh(legGeo, woodMat);
            legB.position.set(0, 3.5, -2);
            legB.rotation.x = -0.3;
            legB.castShadow = true;
            boardGroup.add(legL, legR, legB);

            // Double sized canvas
            const canvas = document.createElement('canvas');
            canvas.width = 1024; canvas.height = 512;
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#1a301a'; // Dark chalkboard green
            ctx.fillRect(0,0,1024,512);
            ctx.strokeStyle = '#5c4033';
            ctx.lineWidth = 48;
            ctx.strokeRect(0,0,1024,512);
            ctx.fillStyle = '#ffb6c1'; // Pink chalk text
            ctx.font = 'bold 88px "Segoe UI", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Press for Magic', 512, 280);

            const tex = new THREE.CanvasTexture(canvas);
            const boardMesh = new THREE.Mesh(
                new THREE.PlaneGeometry(5, 2.5),
                new THREE.MeshLambertMaterial({map: tex})
            );
            boardMesh.position.set(0, 4.4, 0.1);
            boardMesh.userData.isMagicButton = true;
            boardGroup.add(boardMesh);

            // OPTIMIZATION: Add to raycast targets
            interactiveObjects.push(boardMesh);

            const boardBack = new THREE.Mesh(new THREE.PlaneGeometry(5, 2.5), woodMat);
            boardBack.position.set(0, 4.4, 0.08);
            boardBack.rotation.y = Math.PI;
            boardGroup.add(boardBack);

            // Scale the chalkboard to accurate standing sign size
            boardGroup.scale.set(2.5, 2.5, 2.5);

            boardGroup.position.set(22, getTerrainHeight(22, 5), 5);
            boardGroup.rotation.y = -Math.PI / 5 + 0.2;
            scene.add(boardGroup);
        }

        function createButterflies() {
            for(let i=0; i<6; i++){
                const b = new THREE.Group();
                const wingGeo = new THREE.PlaneGeometry(0.3, 0.4);
                wingGeo.translate(0.15, 0, 0);
                const colors = [0xff69b4, 0x00ffff, 0xffd700, 0xff4500, 0xda70d6];
                const wingMat = new THREE.MeshBasicMaterial({
                    color: colors[i%colors.length], side: THREE.DoubleSide, transparent: true, opacity: 0.9
                });
                const leftWing = new THREE.Mesh(wingGeo, wingMat);
                const rightWing = new THREE.Mesh(wingGeo, wingMat);
                rightWing.rotation.y = Math.PI;
                const body = new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.02, 0.3), new THREE.MeshBasicMaterial({color: 0x222222}));
                body.rotation.x = Math.PI/2;
                b.add(leftWing, rightWing, body);

                b.position.set((Math.random()-0.5)*12, 3 + Math.random()*4, (Math.random()-0.5)*12);
                b.userData = {
                    leftWing, rightWing, timeOffset: Math.random()*100,
                    baseY: b.position.y, angle: Math.random() * Math.PI * 2,
                    radius: 2 + Math.random()*6, speed: 0.01 + Math.random()*0.02
                };
                b.visible = false;
                scene.add(b);
                butterflies.push(b);
            }
        }

        function createFireflies() {
            const count = 300;
            const geo = new THREE.BufferGeometry();
            const pos = new Float32Array(count * 3);
            for(let i=0; i<count; i++){
                pos[i*3] = (Math.random()-0.5)*80;
                pos[i*3+1] = 0.5 + Math.random()*15;
                pos[i*3+2] = (Math.random()-0.5)*80;
            }
            geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
            const mat = new THREE.PointsMaterial({
                color: 0xcfff04,
                size: 0.5,
                transparent: true,
                opacity: 1.0,
                blending: THREE.AdditiveBlending,
                depthWrite: false // Enhances the glowing effect
            });
            fireflies = new THREE.Points(geo, mat);
            fireflies.visible = false;
            scene.add(fireflies);
        }

        function createPinkParticles() {
            const count = 400;
            const geo = new THREE.BufferGeometry();
            const pos = new Float32Array(count * 3);
            for(let i=0; i<count; i++) {
                pos[i*3] = (Math.random()-0.5)*20;
                pos[i*3+1] = Math.random()*12;
                pos[i*3+2] = (Math.random()-0.5)*20;
            }
            geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
            const mat = new THREE.PointsMaterial({
                color: 0xff69b4,
                size: 0.3,
                transparent: true,
                opacity: 0.9,
                blending: THREE.AdditiveBlending,
                depthWrite: false // Enhances glowing effect
            });
            pinkParticles = new THREE.Points(geo, mat);
            pinkParticles.visible = false;
            scene.add(pinkParticles);
        }

        function createFloatingHearts() {
            const x = 0, y = 0;
            const heartShape = new THREE.Shape();
            heartShape.moveTo( x + 5, y + 5 );
            heartShape.bezierCurveTo( x + 5, y + 5, x + 4, y, x, y );
            heartShape.bezierCurveTo( x - 6, y, x - 6, y + 7, x - 6, y + 7 );
            heartShape.bezierCurveTo( x - 6, y + 11, x - 3, y + 15.4, x + 5, y + 19 );
            heartShape.bezierCurveTo( x + 12, y + 15.4, x + 16, y + 11, x + 16, y + 7 );
            heartShape.bezierCurveTo( x + 16, y + 7, x + 16, y, x + 10, y );
            heartShape.bezierCurveTo( x + 7, y, x + 5, y + 5, x + 5, y + 5 );

            const extrudeSettings = { depth: 1, bevelEnabled: true, bevelSegments: 2, steps: 1, bevelSize: 1, bevelThickness: 1 };
            const geo = new THREE.ExtrudeGeometry( heartShape, extrudeSettings );
            // Significantly scale up the hearts
            geo.scale(0.04, 0.04, 0.04);
            geo.rotateX(Math.PI);
            geo.translate(0, 0.2, 0);

            // Add emissive property so they self-illuminate and glow in the dark
            const mat = new THREE.MeshPhongMaterial({
                color: 0xff1493,
                emissive: 0xff1493,
                emissiveIntensity: 0.8,
                transparent: true,
                opacity: 0.9,
                side: THREE.DoubleSide
            });
            floatingHearts = new THREE.InstancedMesh(geo, mat, 40); // Slightly fewer since they are much bigger

            const dummy = new THREE.Object3D();
            for(let i=0; i<80; i++){
                dummy.position.set((Math.random()-0.5)*20, Math.random()*15, (Math.random()-0.5)*20);
                dummy.rotation.y = Math.random() * Math.PI;
                dummy.rotation.x = (Math.random() - 0.5) * 0.5;
                dummy.updateMatrix();
                floatingHearts.setMatrixAt(i, dummy.matrix);
            }
            floatingHearts.visible = false;
            scene.add(floatingHearts);
        }

        function launchFirework() {
            const fwGroup = new THREE.Group();
            const color = new THREE.Color().setHSL(Math.random(), 1, 0.6);
            const mat = new THREE.PointsMaterial({color: color, size: 0.3, transparent: true, blending: THREE.AdditiveBlending});
            const geo = new THREE.BufferGeometry();
            const positions = [];
            const velocities = [];
            for(let i=0; i<200; i++){
                const t = Math.random() * Math.PI * 2;
                // Parametric Heart Equation
                const hx = 16 * Math.pow(Math.sin(t), 3);
                const hy = 13 * Math.cos(t) - 5 * Math.cos(2*t) - 2 * Math.cos(3*t) - Math.cos(4*t);

                velocities.push(hx * 0.03 + (Math.random()-0.5)*0.1, hy * 0.03 + (Math.random()-0.5)*0.1, (Math.random()-0.5)*0.8);
                positions.push(0,0,0);
            }
            geo.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
            const points = new THREE.Points(geo, mat);
            fwGroup.add(points);
            fwGroup.position.set((Math.random()-0.5)*60, 25 + Math.random()*15, (Math.random()-0.5)*60);
            scene.add(fwGroup);
            fireworks.push({points, velocities, age: 0, life: 100});
        }

        function toggleNightMagic() {
            if (isNightMagic || isDayMagic) return; // Ignore if any magic is happening
            isNightMagic = true;

            butterflies.forEach(b => b.visible = true);
            fireflies.visible = true;
            floatingHearts.visible = true;
            pinkParticles.visible = true;

            // Stop magic exactly after 7 seconds
            clearTimeout(nightMagicTimeout);
            nightMagicTimeout = setTimeout(() => {
                isNightMagic = false;
            }, 7000);
        }

        function toggleDayMagic() {
            if (isNightMagic || isDayMagic) return; // Ignore if any magic is happening
            isDayMagic = true;

            butterflies.forEach(b => b.visible = true);
            pinkParticles.visible = true;
            floatingHearts.visible = true; // Include floating hearts in Day Magic!

            // Stop magic exactly after 7 seconds
            clearTimeout(dayMagicTimeout);
            dayMagicTimeout = setTimeout(() => {
                isDayMagic = false;
            }, 7000);
        }

        function onPointerDown(event) {
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
            raycaster.setFromCamera(mouse, camera);

            // OPTIMIZATION: Raycast ONLY against interactive objects instead of 80,000+ grass meshes
            const intersects = raycaster.intersectObjects(interactiveObjects, false);
            for (let i = 0; i < intersects.length; i++) {
                if (intersects[i].object.userData.isMagicButton) {
                    toggleNightMagic();
                    break;
                } else if (intersects[i].object.userData.isClickableBouquet) {
                    toggleDayMagic();
                    break;
                }
            }
        }

        function onPointerMove(event) {
            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
            raycaster.setFromCamera(mouse, camera);

            // OPTIMIZATION: Raycast ONLY against interactive objects
            const intersects = raycaster.intersectObjects(interactiveObjects, false);
            let hoveringButton = false;
            for (let i = 0; i < intersects.length; i++) {
                if (intersects[i].object.userData.isMagicButton || intersects[i].object.userData.isClickableBouquet) {
                    hoveringButton = true;
                    break;
                }
            }
            document.body.style.cursor = hoveringButton ? 'pointer' : 'default';
        }

        function onWindowResize() {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        }

        function animate() {
            requestAnimationFrame(animate);

            const time = Date.now();

            // Animate flowers and grasses swaying in the wind
            flowers.forEach(element => {
                const ud = element.userData;
                const swayX = Math.sin(time * ud.swaySpeed + ud.randomSeed) * 0.05;
                const swayZ = Math.cos(time * ud.swaySpeed + ud.randomSeed) * 0.05;

                const multiplier = ud.isBouquet ? 0.4 : 1.0;

                element.rotation.x = ud.originalRotX + swayX * multiplier;
                element.rotation.z = ud.originalRotZ + swayZ * multiplier;
            });

            // Animate floating pollen
            if (particles) {
                const positions = particles.geometry.attributes.position.array;
                for (let i = 1; i < positions.length; i += 3) {
                    positions[i] += 0.01;
                    positions[i-1] += Math.sin(time * 0.001 + positions[i]) * 0.01;
                    if (positions[i] > 20) {
                        positions[i] = 0;
                    }
                }
                particles.geometry.attributes.position.needsUpdate = true;
            }

            // Animate Bouquet Position & Floating
            if (bouquetGroup) {
                // Determine the dynamic floating center position (Lowered closer to ground, inside massive arch)
                posCenter.x = 0;
                posCenter.z = -5;
                posCenter.y = 11.0 + Math.sin(time * 0.002) * 0.8; // Reduced height to prevent overlapping

                // Slowly rotate while hovering in the center
                rotCenter.setFromEuler(new THREE.Euler(
                    0.1 * Math.sin(time * 0.001),
                    time * 0.0005,
                    0.1 * Math.cos(time * 0.001)
                ));

                // Smoothly interpolate between bench and center
                bouquetGroup.position.lerpVectors(posBench, posCenter, floatProgress);
                bouquetGroup.quaternion.slerpQuaternions(rotBench, rotCenter, floatProgress);

                // Make all 4 spotlights track the bouquet
                magicLights.forEach(light => {
                    light.target.position.copy(bouquetGroup.position);
                    light.target.position.y += 3; // Aim at the flowers, not the stems
                });
            }

            // Animate the "Click me!" bubble
            if (clickMeSprite) {
                clickMeSprite.position.y = 12.5 + Math.sin(time * 0.003) * 0.3; // Gentle bobbing adjusted for new height
                if (floatProgress > 0) {
                    clickMeSprite.material.opacity = Math.max(0, 1 - (floatProgress * 5)); // Fade out quickly
                } else {
                    clickMeSprite.material.opacity = 1; // Fade back in when resting
                }
            }

            // --- Floating Progress (Triggers for both Day & Night Magic) ---
            const isFloating = isNightMagic || isDayMagic;
            if (isFloating && floatProgress < 1) {
                floatProgress += 0.015;
            } else if (!isFloating && floatProgress > 0) {
                floatProgress -= 0.015;
                if (floatProgress <= 0) {
                    floatProgress = 0;
                    butterflies.forEach(b => b.visible = false);
                    pinkParticles.visible = false;
                    floatingHearts.visible = false; // Hide floating hearts when magic finishes
                }
            }

            // --- Night Progress (Triggers only for Night Magic) ---
            if (isNightMagic && nightProgress < 1) {
                nightProgress += 0.015;
            } else if (!isNightMagic && nightProgress > 0) {
                nightProgress -= 0.015;
                if (nightProgress <= 0) {
                    nightProgress = 0;
                    fireflies.visible = false;
                }
            }

            // Render Animations based on Float Progress
            if (floatProgress > 0) {
                // Animate Pink Particles
                if (pinkParticles.visible) {
                    const pp = pinkParticles.geometry.attributes.position.array;
                    for(let i=0; i<pp.length; i+=3) {
                        pp[i+1] += 0.02; // drift up
                        pp[i] += Math.sin(time*0.001 + i)*0.01;
                        if(pp[i+1] > 15) pp[i+1] = 0;
                    }
                    pinkParticles.geometry.attributes.position.needsUpdate = true;
                }

                // Animate Butterflies
                butterflies.forEach(b => {
                    if (b.visible) {
                        const ud = b.userData;
                        ud.leftWing.rotation.y = Math.sin(time*0.015 + ud.timeOffset)*1.2;
                        ud.rightWing.rotation.y = Math.PI - Math.sin(time*0.015 + ud.timeOffset)*1.2;
                        ud.angle += ud.speed;
                        b.position.x = Math.cos(ud.angle) * ud.radius;
                        b.position.z = Math.sin(ud.angle) * ud.radius;
                        b.position.y = ud.baseY + Math.sin(time*0.003 + ud.timeOffset)*0.8;
                        b.rotation.y = -ud.angle + Math.PI;
                    }
                });

                // Animate Floating Hearts (Moved here so it works in both Day & Night modes)
                if (floatingHearts.visible) {
                    const dummy = new THREE.Object3D();
                    const mat4 = new THREE.Matrix4();
                    const pos = new THREE.Vector3();
                    const rot = new THREE.Quaternion();
                    const scale = new THREE.Vector3();

                    for(let i=0; i<80; i++) {
                        floatingHearts.getMatrixAt(i, mat4);
                        mat4.decompose(pos, rot, scale);
                        pos.y += 0.03;
                        pos.x += Math.sin(time*0.002 + i)*0.015;
                        if(pos.y > 20) pos.y = -2;

                        dummy.position.copy(pos);
                        dummy.quaternion.copy(rot);
                        // gentle spin
                        dummy.rotateY(0.02);
                        dummy.scale.copy(scale);
                        dummy.updateMatrix();
                        floatingHearts.setMatrixAt(i, dummy.matrix);
                    }
                    floatingHearts.instanceMatrix.needsUpdate = true;
                }
            }

            // Render Animations based on Night Progress
            if (nightProgress > 0) {
                // Day to Night transition
                scene.background.lerpColors(bgDay, bgNight, nightProgress);
                scene.fog.color.lerpColors(fogDay, fogNight, nightProgress);
                hemiLight.intensity = 0.8 * (1 - nightProgress) + 0.1 * nightProgress;
                dirLight.intensity = 0.8 * (1 - nightProgress) + 0.05 * nightProgress;

                // Illuminate the Bouquet from all angles during night magic mode
                magicLights.forEach(light => {
                    light.intensity = 4 * nightProgress;
                });

                // Animate Fireflies
                if (fireflies.visible) {
                    const fp = fireflies.geometry.attributes.position.array;
                    for(let i=0; i<fp.length; i+=3) {
                        fp[i+1] += Math.sin(time*0.002 + i)*0.005;
                        fp[i] += Math.sin(time*0.001 + i)*0.005;
                    }
                    fireflies.geometry.attributes.position.needsUpdate = true;
                    fireflies.material.opacity = 0.5 + Math.sin(time * 0.005) * 0.4;
                }

                // Spawn Heart Fireworks randomly
                if (isNightMagic && Math.random() < 0.035) {
                    launchFirework();
                }
            } else {
                // Ensure day colors when not transitioning to night
                scene.background.copy(bgDay);
                scene.fog.color.copy(fogDay);
                hemiLight.intensity = 0.8;
                dirLight.intensity = 0.8;
                magicLights.forEach(light => light.intensity = 0); // Turn off lights in day
            }

            // Update Fireworks Physics
            for(let i=fireworks.length-1; i>=0; i--) {
                const fw = fireworks[i];
                fw.age++;
                const pos = fw.points.geometry.attributes.position.array;
                for(let j=0; j<pos.length/3; j++) {
                    pos[j*3] += fw.velocities[j*3];
                    pos[j*3+1] += fw.velocities[j*3+1];
                    pos[j*3+2] += fw.velocities[j*3+2];
                    fw.velocities[j*3+1] -= 0.001; // Gravity
                }
                fw.points.geometry.attributes.position.needsUpdate = true;
                fw.points.material.opacity = 1 - (fw.age / fw.life);
                if(fw.age >= fw.life) {
                    scene.remove(fw.points.parent);
                    fireworks.splice(i, 1);
                }
            }

            controls.update();
            renderer.render(scene, camera);
        }
    </script>
</body>
</html>
