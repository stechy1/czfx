<?php

namespace app\model\snippet;


use app\model\html\element\AnchorElement;
use app\model\html\element\DivElement;
use app\model\html\element\SmallElement;
use app\model\html\HtmlBuilder;
use app\model\util\DateUtils;
use app\model\util\StringUtils;

class PostSnippet extends ASnippet {

    const POST_CONTENT_LENGTH = 60;

    private $userID;
    private $userNick;
    private $postID;
    private $postContent;
    private $postDate;
    private $postCategoryURL;
    private $postTopicURL;
    private $postTopicSubject;


    /**
     * PostSnippet constructor.
     * @param $data array
     */
    public function __construct($data) {
        $this->userID = $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->postID = $data['post_id'];
        $this->postContent = $data['post_content'];
        $this->postDate = $data['post_date'];
        $this->postCategoryURL = $data['category_url'];
        $this->postTopicURL = $data['topic_url'];
        $this->postTopicSubject = $data['topic_subject'];
    }

    /**
     * Build the snippet.
     * @return ISnippet
     */
    public function build() {
        $panel = (new DivElement([
            (new DivElement(
                (new SmallElement([
                    (new AnchorElement($this->postTopicSubject))->setLocation('forum/show-posts/'
                        . $this->postCategoryURL . '/'
                        . $this->postTopicURL
                        . "#post-$this->postID"),
                    ' - ',
                    (new AnchorElement($this->userNick))->setLocation('profile/' . $this->userID)
                ]))
            ))->addClass('panel-heading'),
            (new DivElement(
                (new SmallElement(
                    StringUtils::shorten($this->postContent, self::POST_CONTENT_LENGTH)
                ))
            ))->addClass('panel-body'),
            (new DivElement(
                (new SmallElement(
                    DateUtils::formatDate($this->postDate)
                ))
            ))->addClass('panel-footer')
        ]))->addClass(['panel', 'post']);

        $builder = new HtmlBuilder($panel);
        $this->html = $builder->render();
    }
}