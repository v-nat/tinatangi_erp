/**
 * SweetAlert2 shim — exposes the CDN-loaded Swal instance as an ES module.
 * This lets feature files write `import Swal from 'sweetalert2'` without
 * bundling a second copy of Swal alongside the CDN version.
 */
const Swal = window.Swal;
export { Swal as default };
