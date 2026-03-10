/**
 * jQuery shim — exposes the CDN-loaded jQuery instance as an ES module.
 * This lets feature files write `import $ from 'jquery'` while still using
 * the same jQuery that CDN-loaded DataTables / Bootstrap are attached to.
 */
const $ = window.jQuery;
export { $ as default };
