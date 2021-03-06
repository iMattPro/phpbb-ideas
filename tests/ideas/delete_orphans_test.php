<?php
/**
 *
 * Ideas extension for the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\ideas\tests\ideas;

class delete_orphans_test extends \phpbb\ideas\tests\ideas\ideas_base
{
	public function test_delete_orphans()
	{
		$idea = $this->get_idea_object();
		$ideas = $this->get_ideas_object();

		// First lets get a count of the good ideas
		$ideas->get_ideas();
		$valid_ideas = $ideas->get_idea_count();

		// Check the orphan ideas exists
		self::assertNotEmpty($idea->get_idea(6));
		self::assertNotEmpty($idea->get_idea(7));

		// Delete orphans
		self::assertEquals(2, $ideas->delete_orphans());

		// Confirm orphan ideas no longer exists
		self::assertFalse($idea->get_idea(6));
		self::assertFalse($idea->get_idea(7));

		// Check that all votes from orphan ideas are removed
		$sql = 'SELECT COUNT(idea_id) as num_ideas FROM phpbb_ideas_votes WHERE idea_id IN(6, 7)';
		$result = $this->db->sql_query($sql);
		$num_ideas = (int) $this->db->sql_fetchfield('num_ideas');
		$this->db->sql_freeresult($result);
		self::assertEquals(0, $num_ideas);

		// Confirm that only the orphans were deleted
		$ideas->get_ideas();
		self::assertEquals($valid_ideas, $ideas->get_idea_count());

		// Delete orphans again, confirming there's nothing to delete
		self::assertEquals(0, $ideas->delete_orphans());
	}
}
