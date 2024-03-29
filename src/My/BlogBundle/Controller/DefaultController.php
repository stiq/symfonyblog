<?php

namespace My\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use My\BlogBundle\Entity\Post;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use My\BlogBundle\Form\PostType;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getEntityManager();
		$posts = $em->getRepository('MyBlogBundle:Post')->findAll();
        return $this->render('MyBlogBundle:Default:index.html.twig', array('posts' => $posts));
    }
	
	public function newAction()
	{
		// フォームのビルド
		//$form = $this->createFormBuilder(new Post())
			//->add('title')
			//->add('body')
			//->getForm();
		$form = $this->createForm(new PostType(), new Post());	
		// バリデーション
		$request = $this->getRequest();
		if('POST' === $request->getMethod()) {
			$form->bindRequest($request);
			if($form->isValid()){
				// エンティティを永続化
				$post = $form->getData();
				//$post->setCreatedAt(new \DateTime());
				//$post->setUpdatedAt(new \DateTime());
				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($post);
				$em->flush();
				$this->get('session')->setFlash('my_blog', '記事を追加しました');
				return $this->redirect($this->generateUrl('blog_index'));
			}
		}
		// 描画
		return $this->render('MyBlogBundle:Default:new.html.twig', array(
			'form' => $form->createView(),
		));
	}
	
	public function showAction($id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$post = $em->find('MyBlogBundle:Post', $id);
		return $this->render('MyBlogBundle:Default:show.html.twig', array('post' => $post));
	}

	public function deleteAction($id){
		$em = $this->getDoctrine()->getEntityManager();
		$post = $em->find('MyBlogBundle:Post', $id);
		if(!$post){
			throw new NotFoundHttpException('The post does not exist.');
		}
		$em->remove($post);
		$em->flush();
		$this->get('session')->setFlash('my_blog', '記事を削除しました');
		return $this->redirect($this->generateUrl('blog_index'));
	}
	
	public function editAction($id){
		// DBから取得
		$em = $this->getDoctrine()->getEntityManager();
		$post = $em->find('MyBlogBundle:Post', $id);
		if (!$post){
			throw new NotFoundHttpException('The post does not exist.');
		}
		
		// フォームのビルド
		//$form = $this->createFormBuilder($post)
			//->add('title')
			//->add('body')
			//->getForm();
		$form = $this->createForm(new PostType(), $post);
		
		// バリデーション
		$request = $this->getRequest();
		if ('POST' === $request->getMethod()){
			$form->bindRequest($request);
			if($form->isValid()) {
				// 更新されたエンティティをデータベースに保存
				$post = $form->getData();
				//$post->setUpdatedAt(new \DateTime());
				$em->flush();
				$this->get('session')->setFlash('my_blog', '記事を編集しました');
				return $this->redirect($this->generateUrl('blog_index'));
			}
		}
		
		// 描画
		return $this->render('MyBlogBundle:Default:edit.html.twig', array(
			'post' => $post,
			'form' => $form->createView(),
		));
	}
}
