let wpsp_deactivated_blocks = wpsp_deactivate_blocks.deactivated_blocks
// If we are recieving an object, let's convert it into an array.
if (wpsp_deactivated_blocks.length) {
	if (typeof wp.blocks.unregisterBlockType !== "undefined") {
		for (block_index in wpsp_deactivated_blocks) {
			wp.blocks.unregisterBlockType(wpsp_deactivated_blocks[block_index]);
		}
	}

}
