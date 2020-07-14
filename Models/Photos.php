<?php
namespace Models;

use \Core\Model;

class Photos extends Model {

    public function getFeedColletion($ids, $offset, $per_page){
        $array = array();
        $users = new Users();

        if(count($ids) > 0){

            $sql = "SELECT * FROM photos WHERE id_user IN 
            (".implode(',', $ids).") ORDER BY id DESC
            LIMIT ".$offset.", ".$per_page;

            $sql = $this->db->query($sql);

            if($sql->rowCount() > 0){

                $array = $sql->fetchAll(\PDO::FETCH_ASSOC);

                foreach($array as $k => $item){
                    $user_info = $users->getInfo($item['id_user']);

                    $array[$k]['name'] = $user_info['name'];
                    $array[$k]['avatar'] = $user_info['avatar'];
                    $array[$k]['url'] = BASE_URL.'media/photos/'.$item['url'];

                    $array[$k]['like_count'] = $this->getLikeCount($item['id']);
                    $array[$k]['comments'] = $this->getComments($item['id']);
                }
            }
        }

        return $array;
    }

    public function getComments($id_photo){
        $array = array();

        $sql = "SELECT photos_comments.*, users.name FROM photos_comments 
        LEFT JOIN users ON users.id = photos_comments.id_user 
        WHERE photos_comments.id_photo = :id";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id', $id_photo);
        $sql->execute();

        if($sql->rowCount() > 0){

            $array = $sql->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $array;
    }

    public function getLikeCount($id_photo){
        $sql = "SELECT COUNT(*) as c FROM photos_likes WHERE id_photo = :id";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id', $id_photo);
        $sql->execute();
        $info = $sql->fetch();

        return $info['c'];
    }

    public function getPhotosCount($id_user){
        $sql = "SELECT COUNT(*) as c FROM photos WHERE id_user = :id";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id', $id_user);
        $sql->execute();
        $info = $sql->fetch();

        return $info['c'];
    }

    public function deleteAll($id_user){

        $sql = "DELETE FROM photos WHERE id_user = :id_user";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        $sql = "DELETE FROM photos_comments WHERE id_user = :id_user";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        $sql = "DELETE FROM photos_likes WHERE id_user = :id_user";
        $sql = $this->db->prepare($sql);
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();
    
    }

}