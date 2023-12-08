<?php

namespace App\Repositories;

class ExchangeRateRepository
{

    /**
     * Get all posts.
     *
     * @return Post $post
     */
    public function getAll()
    {
        return $this->post
            ->get();
    }

    /**
     * Get post by id
     *
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->post
            ->where('id', $id)
            ->get();
    }

    /**
     * Save Post
     *
     * @param $data
     * @return Post
     */
    public function save($data)
    {
        $post = new $this->post;

        $post->title = $data['title'];
        $post->description = $data['description'];

        $post->save();

        return $post->fresh();
    }

    /**
     * Update Post
     *
     * @param $data
     * @return Post
     */
    public function update($data, $id)
    {

        $post = $this->post->find($id);

        $post->title = $data['title'];
        $post->description = $data['description'];

        $post->update();

        return $post;
    }

    /**
     * Update Post
     *
     * @param $data
     * @return Post
     */
    public function delete($id)
    {

        $post = $this->post->find($id);
        $post->delete();

        return $post;
    }

}
