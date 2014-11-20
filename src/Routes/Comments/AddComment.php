<?php
namespace Szurubooru\Routes\Comments;
use Szurubooru\Controllers\ViewProxies\CommentViewProxy;
use Szurubooru\Controllers\ViewProxies\PostViewProxy;
use Szurubooru\Helpers\InputReader;
use Szurubooru\Privilege;
use Szurubooru\Services\CommentService;
use Szurubooru\Services\PostService;
use Szurubooru\Services\PrivilegeService;

class AddComment extends AbstractCommentRoute
{
	private $privilegeService;
	private $postService;
	private $commentService;
	private $commentViewProxy;
	private $postViewProxy;
	private $inputReader;

	public function __construct(
		PrivilegeService $privilegeService,
		PostService $postService,
		CommentService $commentService,
		CommentViewProxy $commentViewProxy,
		PostViewProxy $postViewProxy,
		InputReader $inputReader)
	{
		$this->privilegeService = $privilegeService;
		$this->postService = $postService;
		$this->commentService = $commentService;
		$this->commentViewProxy = $commentViewProxy;
		$this->postViewProxy = $postViewProxy;
		$this->inputReader = $inputReader;
	}

	public function getMethods()
	{
		return ['POST'];
	}

	public function getUrl()
	{
		return '/api/comments/:postNameOrId';
	}

	public function work()
	{
		$this->privilegeService->assertPrivilege(Privilege::ADD_COMMENTS);

		$post = $this->postService->getByNameOrId($this->getArgument('postNameOrId'));
		$comment = $this->commentService->createComment($post, $this->inputReader->text);
		return $this->commentViewProxy->fromEntity($comment, $this->getCommentsFetchConfig());
	}
}
