<?php
/**
 * Description of story_model
 *
 * @author mike.pearce
 */
class Story_model extends Model {

    private $_orderBy;
    private $_dir;
    private $_isDone;

    /**
     * Constructor, just setup the class vars
     */
    public function __construct()
    {
        $this->_orderBy = FALSE;
        $this->_dir     = FALSE;
        $this->_isDone  = FALSE;
    }

    /**
     * Get all the themeNames
     * @return array() $moo
     */
    public function getThemeNames()
    {

        // Get the storynames/themes
        $this->db->select('id, themeName')
            ->from('themes')
            ->order_by('themeName', 'DESC');
        $query = $this->db->get();
        $a = $query->result();
        $moo = array('' => '--');
        foreach($a AS $row)
        {
            $moo[$row->id] = $row->themeName;
        }
        return $moo;
    }

    public function getStoriesInOrder($id)
    {
        // It appears that this query doesn't work as expected
         $query = $this->db->query('SELECT
                                        stories.nickname,
                                        stories.estimate,
                                        stories.themeId,
                                        stories.id,
                                        themes.priorityOrder
                                    FROM
                                        stories
                                    JOIN
                                        themes ON themes.id = stories.themeId
                                    WHERE
                                        stories.deleted = 0
                                        AND
                                        stories.done = 0
                                        AND
                                        stories.themeId = '. $id .'
                                    ORDER BY
                                        stories.priorityOrder
                                        /**
                                        FIND_IN_SET(
                                            stories.id,
                                            themes.priorityOrder
                                        )
                                        **/
                                    ');
         return $query->result();
    }

    /**
     * Set the orderby and direction
     * @param string $orderby
     * @param string $dir
     */
    public function setOrderBy($orderby, $dir = 'DESC')
    {
        $this->_orderBy  = $orderby;
        $this->_dir      = $dir;
    }

    /**
     * Set whether we should return done stories or not.
     * @param int $done
     */
    public function setDone($done = 0)
    {
        $this->_isDone = $done;
    }

    /**
     * "Delete" a row
     * @param int $id
     */
    public function deleteStory($id)
    {
        $this->db->where('id', $id);
        $this->db->update('stories', array('deleted' =>1, 'date_modified' => date('Y-m-d H:i:s')));

    }

    /**
     * Mark Story as Done
     * @param int $id
     */
    public function markStoryDone($id, $done)
    {
        $this->db->where('id', $id);
        $this->db->update('stories', array('done' => ($done == 'FALSE' ? 0 : 1), 'date_modified' => date('Y-m-d H:i:s')));
    }

    /**
     * Get all the stories based on
     *  - The themeid or not
     *  - Order it, or not.
     *  - Add a limit, or not
     * @param int $themeId
     * @param int $limit
     * @return object result set
     */
    public function getAllStories($themeId = FALSE, $limit = 5)
    {
       // Get the stories
        $this->db->select('stories.id, 
                            themes.themeName,
                            stories.asA,
                            stories.iNeed,
                            stories.soThat,
                            stories.acceptanceCriteria,
                            stories.estimate,
                            stories.nickname, 
                            stories.done,
                            stories.priorityOrder,
                            stories.date_modified,
                            stories.date_added')
        ->from('stories')
        ->join('themes', 'themes.id = stories.themeid');

        // If we have a theme
        if (FALSE !== $themeId)
        {
            $this->db->where('themes.id', $themeId);
        }

        // If there isn't an orderyby
        if (!$this->_orderBy && !$this->_dir)
        {
            $this->db->order_by('stories.id', 'DESC');
        }
        // Otherwise, use it!
        else {
            $this->db->order_by(
                        $this->_orderBy,
                        $this->_dir
                    );
        }

        // do we want the done items?
        // @todo - this could probably be done with type caseing the _isdone
        // value
        if ($this->_isDone)
        {
            $this->db->where('done', 1);
        }
        else {
            $this->db->where('done', 0);
        }

        // If we have a limit (defaults to 5)
        // @todo Add offsetting for pagination
        if (FALSE !== $limit)
        {
                $this->db->limit($limit);
        }

        // Finally, make sure we don't get deleted once
        $this->db->where('deleted', 0);
                
        $query = $this->db->get();
        return $query->result();
    }


    /**
     * Hi there! This returns a result object of stories based on an array of ids
     * @param array $ids
     * @return object result object
     */
    public function getManyStories($ids)
    {
        // Get the stories
        $this->db->select('stories.id,
                            themes.themeName,
                            stories.asA,
                            stories.iNeed,
                            stories.soThat,
                            stories.acceptanceCriteria,
                            stories.estimate,
                            stories.nickname,
                            stories.done')
                ->from('stories')
                ->join('themes', 'themes.id = stories.themeid')
                ->where_in('stories.id', $ids);

        // Don't get ones we deleted!
        $this->db->where('deleted', 0);
        $this->db->where('done', 0);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get a story based on it's ID
     * @param int $storyId
     * @return object result set
     */
    public function getStory($storyId)
    {
        $this->db->select('stories.id,
                            themes.themeName,
                            stories.themeId,
                            stories.asA,
                            stories.iNeed,
                            stories.soThat,
                            stories.acceptanceCriteria,
                            stories.estimate,
                            stories.nickname,
                            stories.done')
                ->from('stories')
                ->order_by('id', 'DESC')
                ->join('themes', 'themes.id = stories.themeId')
                ->where('stories.id', $storyId);

        // Finally, make sure we don't get deleted once
        $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->result();
    }

    public function savePriorityOrder($id, $priorityOrder = FALSE)
    {

        $data = array(
                   'priorityOrder'      => $priorityOrder
                );

        $this->db->where('id', $id);
        $this->db->update('themes', $data);

        //Also, we'll update the actual rows
        $query = 'UPDATE stories SET priorityOrder =
            CASE ';

        $newp = explode(',', $priorityOrder);
        $x = 1;
        foreach ($newp AS $i)
        {
            $query .= 'WHEN id = '. $i .' THEN '. $x++ .' ';
        }
        $query .= 'END WHERE id IN ('. $priorityOrder .')';

        $q = $this->db->query($query);
        return $this->db->affected_rows();
    }
}
