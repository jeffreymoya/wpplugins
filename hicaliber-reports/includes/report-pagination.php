<?php

class ReportPagination {

    protected $totalPages;
    protected $currentPage;
    protected $range;
    protected $links = [];

    /**
     * start($args) function is called by do_action('pagination')
     * @param $args
     */
    public static function generate($args) {
        $self = new ReportPagination;
        $self->totalPages  = floor($args['count'] / $args['limit']);
        $self->currentPage = $args['paginate'];
        $self->range       = isset($args['range']) ? $args['range'] : $self->totalPages;
        $self->generate_links();

        echo '<div class="large-12 medium-12 small-12 pagination">';
            foreach($self->links as $link)
            {
                if(isset($link['self']))
                {
                    echo "<a class='active'>".$link['self']."</a>";
                }
                else
                {
                    $class = isset($link['class']) ? $link['class'] : '';
                    echo '<a href="'.$link['link'].'" class="'.$class.'">'.$link['label'].'</a>';
                }
            }
        echo '</div>';
    }

    private function generate_links() {
        $links = [];

        $this->generate_back_links();
        $this->generate_self_link();
        $this->generate_front_links();

        return $links;
    }

    private function generate_self_link() {
        $this->links[] = [
            'self' => $this->currentPage
        ];
    }

    private function generate_front_links() {
        for($i=1; $i<=$this->range; $i++) {
            if($this->currentPage+$i <= $this->totalPages) {
                $this->links[] = [
                    'label' => $this->currentPage+$i,
                    'link'  => $this->generate_parameters($this->currentPage+$i)
                ];
            }
        }

        if($this->currentPage < $this->totalPages) {
            $this->links[] = [
                'label' => '>',
                'link'  => $this->generate_parameters($this->currentPage + 1),
                'class' => 'paginate-arrow'
            ];
            $this->links[] = [
                'label' => '>>',
                'link'  => $this->generate_parameters($this->totalPages),
                'class' => 'paginate-arrow'
            ];
        }
    }

    private function generate_back_links() {
        if($this->currentPage > 1) {
            $this->links[] = [
                'label' => '<<',
                'link'  => $this->generate_parameters(1),
                'class' => 'paginate-arrow'
            ];
            $this->links[] = [
                'label' => '<',
                'link'  => $this->generate_parameters($this->currentPage - 1),
                'class' => 'paginate-arrow'
            ];
        }

        for($i=$this->range; $i>=1; $i--) {
            if($this->currentPage-$i >= 1) {
                $this->links[] = [
                    'label' => $this->currentPage-$i,
                    'link'  => $this->generate_parameters($this->currentPage-$i)
                ];
            }
        }
    }

    private function generate_parameters($page) {
        $paramsArray = [];
        $_GET['paginate'] = $page;

        foreach($_GET as $key=>$params) {
            $paramsArray[] = "$key=$params";
        }

        return '?'.implode('&', $paramsArray);
    }

}